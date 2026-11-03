<?php
/**
 * Import Industry Skills Framework from skill_Industries.csv into MySQL
 * System: Survey IPT System
 * Creates:
 *  - industry_skills_framework (Job position master table)
 *  - industry_job_required_skills (Normalized required skills per job position)
 */

require_once __DIR__ . '/config.php';

try {
    $db = getDBConnection();

    echo "--- 1. Creating Database Tables ---\n";

    // 1. Framework Job Position Master Table
    $db->exec("
        CREATE TABLE IF NOT EXISTS `industry_skills_framework` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `focus_area` VARCHAR(100) NOT NULL,
            `job_area` VARCHAR(150) NOT NULL,
            `job_position` VARCHAR(150) NOT NULL,
            `job_description` TEXT NOT NULL,
            `soft_skills_raw` TEXT NOT NULL,
            `technical_skills_raw` TEXT NOT NULL,
            `total_soft_skills` INT DEFAULT 0,
            `total_technical_skills` INT DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY `unique_job_pos` (`focus_area`, `job_area`, `job_position`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 2. Normalized Required Skills Table
    $db->exec("
        CREATE TABLE IF NOT EXISTS `industry_job_required_skills` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `framework_id` INT NOT NULL,
            `skill_name` VARCHAR(150) NOT NULL,
            `skill_type` ENUM('technical', 'soft') NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`framework_id`) REFERENCES `industry_skills_framework`(`id`) ON DELETE CASCADE,
            INDEX `idx_framework_id` (`framework_id`),
            INDEX `idx_skill_name` (`skill_name`),
            INDEX `idx_skill_type` (`skill_type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "Tables 'industry_skills_framework' and 'industry_job_required_skills' ready.\n\n";

    echo "--- 2. Parsing skill_Industries.csv ---\n";
    $csvFile = __DIR__ . '/skill_Industries.csv';
    if (!file_exists($csvFile)) {
        die("Error: skill_Industries.csv not found at $csvFile\n");
    }

    $handle = fopen($csvFile, 'r');
    // Skip line 1 (title header) and line 2 (column headers)
    $titleLine = fgetcsv($handle);
    $columnHeaders = fgetcsv($handle);

    // Clear existing framework data for clean re-import
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $db->exec("TRUNCATE TABLE `industry_job_required_skills`;");
    $db->exec("TRUNCATE TABLE `industry_skills_framework`;");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");

    $stmtInsertFramework = $db->prepare("
        INSERT INTO `industry_skills_framework` 
        (`focus_area`, `job_area`, `job_position`, `job_description`, `soft_skills_raw`, `technical_skills_raw`, `total_soft_skills`, `total_technical_skills`) 
        VALUES (:focus, :area, :pos, :desc, :soft_raw, :tech_raw, :soft_cnt, :tech_cnt)
    ");

    $stmtInsertSkill = $db->prepare("
        INSERT INTO `industry_job_required_skills` 
        (`framework_id`, `skill_name`, `skill_type`) 
        VALUES (:fid, :name, :type)
    ");

    $jobCount = 0;
    $totalSoftInserted = 0;
    $totalTechInserted = 0;

    while (($row = fgetcsv($handle)) !== false) {
        if (empty($row[0])) continue;

        $focusArea      = trim($row[0] ?? '');
        $jobArea        = trim($row[1] ?? '');
        $jobPosition    = trim($row[2] ?? '');
        $jobDescription = trim($row[3] ?? '');
        $softSkillsRaw  = trim($row[4] ?? '');
        $techSkillsRaw  = trim($row[5] ?? '');

        // Parse individual soft skills
        $softSkillsList = array_values(array_filter(array_map('trim', explode(',', $softSkillsRaw)), fn($s) => !empty($s)));
        // Parse individual technical skills
        $techSkillsList = array_values(array_filter(array_map('trim', explode(',', $techSkillsRaw)), fn($s) => !empty($s)));

        $softCount = count($softSkillsList);
        $techCount = count($techSkillsList);

        $stmtInsertFramework->execute([
            'focus'    => $focusArea,
            'area'     => $jobArea,
            'pos'      => $jobPosition,
            'desc'     => $jobDescription,
            'soft_raw' => $softSkillsRaw,
            'tech_raw' => $techSkillsRaw,
            'soft_cnt' => $softCount,
            'tech_cnt' => $techCount
        ]);

        $frameworkId = (int)$db->lastInsertId();
        $jobCount++;

        // Insert normalized soft skills
        foreach ($softSkillsList as $sName) {
            $stmtInsertSkill->execute([
                'fid'  => $frameworkId,
                'name' => $sName,
                'type' => 'soft'
            ]);
            $totalSoftInserted++;
        }

        // Insert normalized tech skills
        foreach ($techSkillsList as $tName) {
            $stmtInsertSkill->execute([
                'fid'  => $frameworkId,
                'name' => $tName,
                'type' => 'technical'
            ]);
            $totalTechInserted++;
        }

        echo "[$jobCount] Imported '$jobPosition' ($jobArea) - Soft: $softCount, Tech: $techCount\n";
    }

    fclose($handle);

    echo "\n=== IMPORT COMPLETED SUCCESSFULLY ===\n";
    echo "Total Job Positions Registered: $jobCount\n";
    echo "Total Soft Skill Requirements: $totalSoftInserted\n";
    echo "Total Technical Skill Requirements: $totalTechInserted\n";

} catch (Exception $e) {
    echo "Error during import: " . $e->getMessage() . "\n";
}
