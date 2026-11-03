<?php
/**
 * Industry Skills Framework Helper
 * Provides querying, matching, scoring, and bold/highlighting utilities
 * for comparing survey respondent skills against industry requirements.
 */

require_once __DIR__ . '/config.php';

class IndustrySkillsHelper {

    /**
     * Retrieve all 36 Industry Job Positions
     */
    public static function getAllJobs() {
        $db = getDBConnection();
        $stmt = $db->query("
            SELECT id, focus_area, job_area, job_position, job_description,
                   soft_skills_raw, technical_skills_raw, total_soft_skills, total_technical_skills
            FROM industry_skills_framework
            ORDER BY focus_area ASC, job_area ASC, id ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Retrieve a specific Industry Job Position with its required skills
     */
    public static function getJobById($jobId) {
        $db = getDBConnection();
        $stmt = $db->prepare("
            SELECT * FROM industry_skills_framework WHERE id = :id LIMIT 1
        ");
        $stmt->execute(['id' => (int)$jobId]);
        $job = $stmt->fetch();

        if (!$job) return null;

        $stmtSkills = $db->prepare("
            SELECT skill_name, skill_type 
            FROM industry_job_required_skills 
            WHERE framework_id = :id
            ORDER BY id ASC
        ");
        $stmtSkills->execute(['id' => (int)$jobId]);
        $all = $stmtSkills->fetchAll();

        $job['soft_skills'] = [];
        $job['technical_skills'] = [];

        foreach ($all as $s) {
            if ($s['skill_type'] === 'soft') {
                $job['soft_skills'][] = $s['skill_name'];
            } else {
                $job['technical_skills'][] = $s['skill_name'];
            }
        }

        return $job;
    }

    /**
     * Match Respondent Skills from Survey with Industry Job Requirements
     *
     * @param array $respondentSkills Array of skill names selected during survey
     * @param int|array $jobOrJobId Framework Job ID or Job Array
     * @return array Matching result with counts, ratios (e.g. '1/3'), and highlighted strings
     */
    public static function evaluateMatch($respondentSkills, $jobOrJobId) {
        $job = is_array($jobOrJobId) ? $jobOrJobId : self::getJobById($jobOrJobId);
        if (!$job) return null;

        // Clean & normalize respondent skills (lowercase trimmed for flexible matching)
        $respMap = [];
        foreach ($respondentSkills as $rs) {
            $cleaned = strtolower(trim($rs));
            if (!empty($cleaned)) {
                $respMap[$cleaned] = true;
            }
        }

        // --- 1. Evaluate Soft Skills ---
        $softSkills = $job['soft_skills'] ?? array_filter(array_map('trim', explode(',', $job['soft_skills_raw'])));
        $softTotal = count($softSkills);
        $softMatched = [];
        $softMissing = [];
        $softHtmlPieces = [];
        $softMdPieces = [];

        foreach ($softSkills as $skill) {
            $isMatch = self::isSkillMatched($skill, $respMap);
            if ($isMatch) {
                $softMatched[] = $skill;
                $softHtmlPieces[] = '<strong class="skill-highlight matched">' . htmlspecialchars($skill) . '</strong>';
                $softMdPieces[] = '**' . $skill . '**';
            } else {
                $softMissing[] = $skill;
                $softHtmlPieces[] = '<span class="skill-unmatched">' . htmlspecialchars($skill) . '</span>';
                $softMdPieces[] = $skill;
            }
        }

        $softMatchedCount = count($softMatched);
        $softRatio = "{$softMatchedCount}/{$softTotal}";
        $softPercent = $softTotal > 0 ? round(($softMatchedCount / $softTotal) * 100) : 0;

        // --- 2. Evaluate Technical Skills ---
        $techSkills = $job['technical_skills'] ?? array_filter(array_map('trim', explode(',', $job['technical_skills_raw'])));
        $techTotal = count($techSkills);
        $techMatched = [];
        $techMissing = [];
        $techHtmlPieces = [];
        $techMdPieces = [];

        foreach ($techSkills as $skill) {
            $isMatch = self::isSkillMatched($skill, $respMap);
            if ($isMatch) {
                $techMatched[] = $skill;
                $techHtmlPieces[] = '<strong class="skill-highlight matched">' . htmlspecialchars($skill) . '</strong>';
                $techMdPieces[] = '**' . $skill . '**';
            } else {
                $techMissing[] = $skill;
                $techHtmlPieces[] = '<span class="skill-unmatched">' . htmlspecialchars($skill) . '</span>';
                $techMdPieces[] = $skill;
            }
        }

        $techMatchedCount = count($techMatched);
        $techRatio = "{$techMatchedCount}/{$techTotal}";
        $techPercent = $techTotal > 0 ? round(($techMatchedCount / $techTotal) * 100) : 0;

        // --- 3. Overall Statistics ---
        $totalRequired = $softTotal + $techTotal;
        $totalMatched = $softMatchedCount + $techMatchedCount;
        $overallPercent = $totalRequired > 0 ? round(($totalMatched / $totalRequired) * 100) : 0;

        return [
            'job_id'                 => $job['id'],
            'job_position'           => $job['job_position'],
            'job_area'               => $job['job_area'],
            'focus_area'             => $job['focus_area'],
            'job_description'        => $job['job_description'],

            // Soft Skills Evaluation
            'soft_total'             => $softTotal,
            'soft_matched_count'     => $softMatchedCount,
            'soft_ratio'             => $softRatio,
            'soft_percent'           => $softPercent,
            'soft_matched_list'      => $softMatched,
            'soft_missing_list'      => $softMissing,
            'soft_highlighted_html'  => implode(', ', $softHtmlPieces),
            'soft_highlighted_md'    => implode(', ', $softMdPieces),

            // Technical Skills Evaluation
            'tech_total'             => $techTotal,
            'tech_matched_count'     => $techMatchedCount,
            'tech_ratio'             => $techRatio,
            'tech_percent'           => $techPercent,
            'tech_matched_list'      => $techMatched,
            'tech_missing_list'      => $techMissing,
            'tech_highlighted_html'  => implode(', ', $techHtmlPieces),
            'tech_highlighted_md'    => implode(', ', $techMdPieces),

            // Overall Summary
            'total_required'         => $totalRequired,
            'total_matched'          => $totalMatched,
            'overall_percent'        => $overallPercent,
            'overall_ratio'          => "{$totalMatched}/{$totalRequired}"
        ];
    }

    /**
     * Retrieve all survey submissions with their selected skills
     */
    public static function getAllSubmissionsWithSkills() {
        $db = getDBConnection();
        $stmt = $db->query("
            SELECT sub.id, sub.respondent_name, sub.respondent_email, sub.annual_intake, sub.submitted_at,
                   sub.state_id, sub.institute_id, sub.course_id,
                   s.state_name, i.institute_name, c.course_name, c.qualification_level, c.campus_branch,
                   GROUP_CONCAT(DISTINCT CASE WHEN sk.skill_type = 'technical' THEN sk.skill_name END SEPARATOR '||') AS tech_skills_raw,
                   GROUP_CONCAT(DISTINCT CASE WHEN sk.skill_type = 'soft' THEN sk.skill_name END SEPARATOR '||') AS soft_skills_raw
            FROM survey_submissions sub
            JOIN states s ON sub.state_id = s.id
            JOIN institutes i ON sub.institute_id = i.id
            JOIN courses c ON sub.course_id = c.id
            LEFT JOIN survey_submission_skills ssk ON sub.id = ssk.submission_id
            LEFT JOIN skills sk ON ssk.skill_id = sk.id
            GROUP BY sub.id
            ORDER BY sub.id DESC
        ");
        $rows = $stmt->fetchAll();

        foreach ($rows as &$r) {
            $r['tech_skills'] = !empty($r['tech_skills_raw']) ? explode('||', $r['tech_skills_raw']) : [];
            $r['soft_skills'] = !empty($r['soft_skills_raw']) ? explode('||', $r['soft_skills_raw']) : [];
            $r['all_skills']  = array_merge($r['tech_skills'], $r['soft_skills']);
        }
        return $rows;
    }

    /**
     * Retrieve single submission with skills by ID
     */
    public static function getSubmissionById($subId) {
        $db = getDBConnection();
        $stmt = $db->prepare("
            SELECT sub.id, sub.respondent_name, sub.respondent_email, sub.annual_intake, sub.submitted_at,
                   sub.state_id, sub.institute_id, sub.course_id,
                   s.state_name, i.institute_name, c.course_name, c.qualification_level, c.campus_branch,
                   GROUP_CONCAT(DISTINCT CASE WHEN sk.skill_type = 'technical' THEN sk.skill_name END SEPARATOR '||') AS tech_skills_raw,
                   GROUP_CONCAT(DISTINCT CASE WHEN sk.skill_type = 'soft' THEN sk.skill_name END SEPARATOR '||') AS soft_skills_raw
            FROM survey_submissions sub
            JOIN states s ON sub.state_id = s.id
            JOIN institutes i ON sub.institute_id = i.id
            JOIN courses c ON sub.course_id = c.id
            LEFT JOIN survey_submission_skills ssk ON sub.id = ssk.submission_id
            LEFT JOIN skills sk ON ssk.skill_id = sk.id
            WHERE sub.id = :id
            GROUP BY sub.id
            LIMIT 1
        ");
        $stmt->execute(['id' => (int)$subId]);
        $row = $stmt->fetch();

        if ($row) {
            $row['tech_skills'] = !empty($row['tech_skills_raw']) ? explode('||', $row['tech_skills_raw']) : [];
            $row['soft_skills'] = !empty($row['soft_skills_raw']) ? explode('||', $row['soft_skills_raw']) : [];
            $row['all_skills']  = array_merge($row['tech_skills'], $row['soft_skills']);
        }
        return $row;
    }

    /**
     * Evaluate a respondent's skills against all 36 industry jobs, ranked by suitability score
     */
    public static function evaluateRespondentAgainstAllJobs($respondentSkills, $focusFilter = null) {
        $allJobs = self::getAllJobs();
        $results = [];

        foreach ($allJobs as $job) {
            if ($focusFilter && $focusFilter !== 'all' && $job['focus_area'] !== $focusFilter) {
                continue;
            }

            $eval = self::evaluateMatch($respondentSkills, $job);
            if ($eval) {
                // Tier assignment
                if ($eval['overall_percent'] >= 50) {
                    $eval['tier'] = 'high';
                    $eval['tier_label'] = 'Sangat Sesuai';
                    $eval['tier_color'] = '#10b981';
                } elseif ($eval['overall_percent'] >= 25) {
                    $eval['tier'] = 'moderate';
                    $eval['tier_label'] = 'Sederhana';
                    $eval['tier_color'] = '#fbbf24';
                } else {
                    $eval['tier'] = 'low';
                    $eval['tier_label'] = 'Rendah';
                    $eval['tier_color'] = '#94a3b8';
                }

                $results[] = $eval;
            }
        }

        // Sort descending by overall percent, then tech matched count
        usort($results, function($a, $b) {
            if ($b['overall_percent'] !== $a['overall_percent']) {
                return $b['overall_percent'] <=> $a['overall_percent'];
            }
            return $b['tech_matched_count'] <=> $a['tech_matched_count'];
        });

        return $results;
    }

    /**
     * Flexible matching helper (handles exact and fuzzy matches like 'PLC' vs 'PLCs')
     */
    public static function isSkillMatched($needle, $haystackMap) {
        $n = strtolower(trim($needle));
        if (isset($haystackMap[$n])) return true;

        // Strip plural 's' or minor variations (e.g. PLC vs PLCs, AutoCAD vs CAD)
        $nSingular = rtrim($n, 's');
        if (isset($haystackMap[$nSingular])) return true;

        foreach ($haystackMap as $key => $v) {
            if ($n === $key) return true;
            if ($nSingular === rtrim($key, 's')) return true;
            // Check direct contains if length > 4 (e.g. 'Project Management' in 'Project & Vendor Management')
            if (strlen($key) > 5 && (strpos($n, $key) !== false || strpos($key, $n) !== false)) {
                return true;
            }
        }

        return false;
    }
}
