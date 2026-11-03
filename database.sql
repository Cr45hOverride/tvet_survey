-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 28, 2026 at 02:30 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `survey_ipt`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password_hash`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$MqBlZvR3XwyeY38hbiLJM.dW4xA86Vhkr/8Kk7d5wBo32EN5kKK4C', 'Pentadbir Utama', 'superadmin', '2026-09-21 06:55:41'),
(3, 'kamal', '$2y$10$3LXaXe48Uv92b3b/duYmueVnL9C54YN11X8BmEQ65DbW.SGzpaMoy', 'kamal harmoni', 'admin', '2026-10-12 10:28:51');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `institute_id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `campus_branch` varchar(255) NOT NULL,
  `qualification_level` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `institute_id`, `course_name`, `campus_branch`, `qualification_level`, `created_at`) VALUES
(1, 1, 'B.C.S. Data Science', 'Main Campus (Gelugor)', 'Degree', '2026-09-21 06:55:41'),
(2, 1, 'B.C.S. Software Engineering', 'Main Campus (Gelugor)', 'Degree', '2026-09-21 06:55:41'),
(3, 1, 'B.C.S. Network & Security', 'Main Campus (Gelugor)', 'Degree', '2026-09-21 06:55:41'),
(4, 1, 'B.Sc. Mathematical Sciences', 'Main Campus (Gelugor)', 'Degree', '2026-09-21 06:55:41'),
(5, 1, 'B.App.Sc. Applied Physics', 'Main Campus (Gelugor)', 'Degree', '2026-09-21 06:55:41'),
(6, 1, 'B.App.Sc. Industrial Chemistry', 'Main Campus (Gelugor)', 'Degree', '2026-09-21 06:55:41'),
(7, 1, 'B.Sc. Housing, Building & Planning (Architecture)', 'Main Campus (Gelugor)', 'Degree', '2026-09-21 06:55:41'),
(8, 1, 'B.Sc. Housing, Building & Planning (Quantity Surveying)', 'Main Campus (Gelugor)', 'Degree', '2026-09-21 06:55:41'),
(9, 1, 'B.Sc. Housing, Building & Planning (Construction Management)', 'Main Campus (Gelugor)', 'Degree', '2026-09-21 06:55:41'),
(10, 1, 'Bachelor of Pharmacy', 'Main Campus (Gelugor)', 'Degree', '2026-09-21 06:55:41'),
(11, 1, 'B.Sc. Pharmaceutical Sciences', 'Main Campus (Gelugor)', 'Degree', '2026-09-21 06:55:41'),
(12, 1, 'B.Eng. Mechanical Engineering', 'Engineering Campus (Nibong Tebal)', 'Degree', '2026-09-21 06:55:41'),
(13, 1, 'B.Eng. Aerospace Engineering', 'Engineering Campus (Nibong Tebal)', 'Degree', '2026-09-21 06:55:41'),
(14, 1, 'B.Eng. Civil Engineering', 'Engineering Campus (Nibong Tebal)', 'Degree', '2026-09-21 06:55:41'),
(15, 1, 'B.Eng. Electrical & Electronics Engineering', 'Engineering Campus (Nibong Tebal)', 'Degree', '2026-09-21 06:55:41'),
(16, 1, 'B.Eng. Mechatronics Engineering', 'Engineering Campus (Nibong Tebal)', 'Degree', '2026-09-21 06:55:41'),
(17, 1, 'B.Eng. Chemical Engineering', 'Engineering Campus (Nibong Tebal)', 'Degree', '2026-09-21 06:55:41'),
(18, 1, 'B.Eng. Materials Engineering', 'Engineering Campus (Nibong Tebal)', 'Degree', '2026-09-21 06:55:41'),
(19, 1, 'B.Eng. Mineral Resources Engineering', 'Engineering Campus (Nibong Tebal)', 'Degree', '2026-09-21 06:55:41'),
(20, 2, 'Diploma in Civil Engineering', 'Permatang Pauh Campus', 'Diploma', '2026-09-21 06:55:41'),
(21, 2, 'Diploma in Electrical Engineering (Power)', 'Permatang Pauh Campus', 'Diploma', '2026-09-21 06:55:41'),
(22, 2, 'Diploma in Electrical Engineering (Electronics)', 'Permatang Pauh Campus', 'Diploma', '2026-09-21 06:55:41'),
(23, 2, 'Diploma in Mechanical Engineering', 'Permatang Pauh Campus', 'Diploma', '2026-09-21 06:55:41'),
(24, 2, 'Diploma in Chemical Engineering', 'Permatang Pauh Campus', 'Diploma', '2026-09-21 06:55:41'),
(25, 2, 'Diploma in Hotel Management', 'Permatang Pauh Campus', 'Diploma', '2026-09-21 06:55:41'),
(26, 2, 'Diploma in Culinary Arts', 'Permatang Pauh Campus', 'Diploma', '2026-09-21 06:55:41'),
(27, 2, 'B.Eng. (Hons) Civil Engineering', 'Permatang Pauh Campus', 'Degree', '2026-09-21 06:55:41'),
(28, 2, 'B.Eng. (Hons) Mechanical Engineering', 'Permatang Pauh Campus', 'Degree', '2026-09-21 06:55:41'),
(29, 2, 'B.Eng. (Hons) Electrical Engineering', 'Permatang Pauh Campus', 'Degree', '2026-09-21 06:55:41'),
(30, 2, 'B.Eng. (Hons) Chemical Engineering', 'Permatang Pauh Campus', 'Degree', '2026-09-21 06:55:41'),
(31, 2, 'Diploma in Pharmacy', 'Bertam Campus', 'Diploma', '2026-09-21 06:55:41'),
(32, 2, 'Foundation in Engineering', 'Bertam Campus', 'Foundation', '2026-09-21 06:55:41'),
(33, 2, 'Foundation in Science', 'Bertam Campus', 'Foundation', '2026-09-21 06:55:41'),
(34, 3, 'Diploma in Civil Engineering', 'Permatang Pauh', 'Diploma', '2026-09-21 06:55:41'),
(35, 3, 'Diploma in Electrical Engineering', 'Permatang Pauh', 'Diploma', '2026-09-21 06:55:41'),
(36, 3, 'Diploma in Electronic Engineering', 'Permatang Pauh', 'Diploma', '2026-09-21 06:55:41'),
(37, 3, 'Diploma in Mechanical Engineering', 'Permatang Pauh', 'Diploma', '2026-09-21 06:55:41'),
(38, 3, 'Diploma in Information Technology (Digital Technology)', 'Permatang Pauh', 'Diploma', '2026-09-21 06:55:41'),
(39, 3, 'Diploma in E-Commerce & Logistics', 'Permatang Pauh', 'Diploma', '2026-09-21 06:55:41'),
(40, 3, 'Diploma in Supply Chain Management', 'Permatang Pauh', 'Diploma', '2026-09-21 06:55:41'),
(41, 3, 'Diploma in Digital Media & Creative Arts', 'Permatang Pauh', 'Diploma', '2026-09-21 06:55:41'),
(42, 4, 'Diploma in Information Technology (Software)', 'Balik Pulau', 'Diploma', '2026-09-21 06:55:41'),
(43, 4, 'Diploma in Information Technology (Network)', 'Balik Pulau', 'Diploma', '2026-09-21 06:55:41'),
(44, 4, 'Diploma in Biotechnology', 'Balik Pulau', 'Diploma', '2026-09-21 06:55:41'),
(45, 4, 'Diploma in Bio-Process Technology', 'Balik Pulau', 'Diploma', '2026-09-21 06:55:41'),
(46, 5, 'Sijil Teknologi Maklumat & Rangkaian', 'Seberang Jaya / Kepala Batas / Bayan Baru', 'Certificate', '2026-09-21 06:55:41'),
(47, 5, 'Sijil Pemasangan Elektrik & Penyejukan (HVAC)', 'Nibong Tebal / Bukit Mertajam', 'Certificate', '2026-09-21 06:55:41'),
(48, 5, 'Sijil Servis Kenderaan Ringan (Automotif)', 'Tasek Gelugor / Bayan Baru', 'Certificate', '2026-09-21 06:55:41'),
(49, 5, 'Sijil Seni Kulinari, Pastri & Dandan Rambut', 'Seberang Jaya / Kepala Batas', 'Certificate', '2026-09-21 06:55:41'),
(50, 6, 'Diploma Technology in Automotive (Penyelenggaraan Kenderaan)', 'Bukit Mertajam', 'Diploma', '2026-09-21 06:55:41'),
(51, 6, 'Diploma Technology in Machining & Kimpalan', 'Bukit Mertajam', 'Diploma', '2026-09-21 06:55:41'),
(52, 6, 'Diploma Technology in Mechatronics & Industrial Automation', 'Bukit Mertajam', 'Diploma', '2026-09-21 06:55:41'),
(53, 6, 'Diploma Technology in Air-Conditioning & Refrigeration (HVAC)', 'Bukit Mertajam', 'Diploma', '2026-09-21 06:55:41'),
(54, 7, 'Diploma Technology Penyelenggaraan Pesawat (Aircraft Maintenance)', 'Kepala Batas', 'Diploma', '2026-09-21 06:55:41'),
(55, 7, 'Sijil Technology Penyelenggaraan Pesawat (Aircraft Maintenance)', 'Kepala Batas', 'Certificate', '2026-09-21 06:55:41'),
(56, 7, 'Sijil Technology Pemesinan CNC', 'Kepala Batas', 'Certificate', '2026-09-21 06:55:41'),
(57, 7, 'Sijil Technology Mold & Die (Tooling)', 'Kepala Batas', 'Certificate', '2026-09-21 06:55:41'),
(58, 7, 'Sijil Technology Automasi Industri', 'Kepala Batas', 'Certificate', '2026-09-21 06:55:41'),
(59, 8, 'DKM Teknologi Elektrik (Penjanaan & Agihan)', 'Perai', 'Diploma', '2026-09-21 06:55:41'),
(60, 8, 'Sijil Teknologi Elektrik (Penjanaan & Agihan)', 'Perai', 'Certificate', '2026-09-21 06:55:41'),
(61, 8, 'Sijil Teknologi Elektronik Industri & Kimpalan', 'Perai', 'Certificate', '2026-09-21 06:55:41'),
(62, 8, 'Sijil Teknologi Automotif', 'Perai', 'Certificate', '2026-09-21 06:55:41'),
(63, 9, 'Diploma Teknologi Kejuruteraan Automotif', 'Seberang Perai', 'Diploma', '2026-09-21 06:55:41'),
(64, 9, 'Sijil Teknologi Kejuruteraan Automotif', 'Seberang Perai', 'Certificate', '2026-09-21 06:55:41'),
(65, 9, 'Sijil Teknologi Kejuruteraan Elektrik & Penyelenggaraan Loji', 'Seberang Perai', 'Certificate', '2026-09-21 06:55:41'),
(66, 10, 'SVM Teknologi Elektrik & Elektronik', 'Butterworth / Seberang Perai / Batu Lanchang', 'SVM/DVM', '2026-09-21 06:55:41'),
(67, 10, 'DVM Teknologi Elektrik & Elektronik', 'Butterworth / Seberang Perai / Batu Lanchang', 'SVM/DVM', '2026-09-21 06:55:41'),
(68, 10, 'SVM Teknologi Kimpalan & Pemesinan Industri', 'Balik Pulau / Nibong Tebal', 'SVM/DVM', '2026-09-21 06:55:41'),
(69, 10, 'DVM Teknologi Kimpalan & Pemesinan Industri', 'Balik Pulau / Nibong Tebal', 'SVM/DVM', '2026-09-21 06:55:41'),
(70, 10, 'SVM Teknologi Pembinaan & Automotif', 'Seberang Perai / Balik Pulau', 'SVM/DVM', '2026-09-21 06:55:41'),
(71, 10, 'DVM Teknologi Pembinaan & Automotif', 'Seberang Perai / Balik Pulau', 'SVM/DVM', '2026-09-21 06:55:41'),
(72, 10, 'SVM Seni Kulinari, Pastri & Pengurusan Perniagaan', 'Batu Lanchang / Nibong Tebal', 'SVM/DVM', '2026-09-21 06:55:41'),
(73, 10, 'DVM Seni Kulinari, Pastri & Pengurusan Perniagaan', 'Batu Lanchang / Nibong Tebal', 'SVM/DVM', '2026-09-21 06:55:41'),
(74, 11, 'Diploma in Precision Engineering & Mechatronics', 'Bayan Lepas', 'Diploma', '2026-09-21 06:55:41'),
(75, 11, 'Diploma in Computer Science', 'Bayan Lepas', 'Diploma', '2026-09-21 06:55:41'),
(76, 11, 'Diploma in Cloud & IoT Technology', 'Bayan Lepas', 'Diploma', '2026-09-21 06:55:41'),
(77, 12, 'B.Sc. Information Technology', 'Sintok', 'Degree', '2026-09-21 06:55:41'),
(78, 12, 'B.Sc. Computer Science', 'Sintok', 'Degree', '2026-09-21 06:55:41'),
(79, 12, 'B.Sc. Multimedia', 'Sintok', 'Degree', '2026-09-21 06:55:41'),
(80, 12, 'B.Sc. Decision Science', 'Sintok', 'Degree', '2026-09-21 06:55:41'),
(81, 12, 'B.Tech. Operations Management', 'Sintok', 'Degree', '2026-09-21 06:55:41'),
(82, 12, 'B.Tech. Logistics & Supply Chain Management', 'Sintok', 'Degree', '2026-09-21 06:55:41'),
(83, 12, 'Bachelor of Business Administration', 'Sintok', 'Degree', '2026-09-21 06:55:41'),
(84, 12, 'Bachelor of Accountancy', 'Sintok', 'Degree', '2026-09-21 06:55:41'),
(85, 12, 'Bachelor of Finance', 'Sintok', 'Degree', '2026-09-21 06:55:41'),
(86, 12, 'Bachelor of Creative Industry Management', 'Sintok', 'Degree', '2026-09-21 06:55:41'),
(87, 13, 'Diploma in Administrative Science (AM110)', 'Merbok', 'Diploma', '2026-09-21 06:55:41'),
(88, 13, 'Diploma in Business Studies', 'Merbok', 'Diploma', '2026-09-21 06:55:41'),
(89, 13, 'Diploma in Banking Studies', 'Merbok', 'Diploma', '2026-09-21 06:55:41'),
(90, 13, 'Diploma in Accountancy', 'Merbok', 'Diploma', '2026-09-21 06:55:41'),
(91, 13, 'Diploma in Art & Design (Graphic & Media Design)', 'Merbok', 'Diploma', '2026-09-21 06:55:41'),
(92, 13, 'Bachelor of Administrative Science (Hons)', 'Merbok', 'Degree', '2026-09-21 06:55:41'),
(93, 14, 'Diploma in Information Technology & Networking', 'Kuala Ketil', 'Diploma', '2026-09-21 06:55:41'),
(94, 14, 'Bachelor in Information Technology & Networking', 'Kuala Ketil', 'Degree', '2026-09-21 06:55:41'),
(95, 14, 'Diploma in Nursing & Health Sciences', 'Kuala Ketil', 'Diploma', '2026-09-21 06:55:41'),
(96, 14, 'Bachelor in Nursing & Health Sciences', 'Kuala Ketil', 'Degree', '2026-09-21 06:55:41'),
(97, 15, 'Diploma in Civil Engineering', 'Jitra', 'Diploma', '2026-09-21 06:55:41'),
(98, 15, 'Diploma in Architecture', 'Jitra', 'Diploma', '2026-09-21 06:55:41'),
(99, 15, 'Diploma in Quantity Surveying', 'Jitra', 'Diploma', '2026-09-21 06:55:41'),
(100, 15, 'Diploma in Electrical & Electronics Engineering', 'Jitra', 'Diploma', '2026-09-21 06:55:41'),
(101, 15, 'Diploma in Mechanical Engineering', 'Jitra', 'Diploma', '2026-09-21 06:55:41'),
(102, 15, 'Diploma in Mechatronics Engineering', 'Jitra', 'Diploma', '2026-09-21 06:55:41'),
(103, 15, 'Diploma in Information Technology (Digital Technology)', 'Jitra', 'Diploma', '2026-09-21 06:55:41'),
(104, 15, 'Diploma in Accountancy', 'Jitra', 'Diploma', '2026-09-21 06:55:41'),
(105, 15, 'Diploma in Marketing', 'Jitra', 'Diploma', '2026-09-21 06:55:41'),
(106, 15, 'Diploma in Business Studies', 'Jitra', 'Diploma', '2026-09-21 06:55:41'),
(107, 16, 'Diploma in Electrical Engineering (Control)', 'Kulim', 'Diploma', '2026-09-21 06:55:41'),
(108, 16, 'Diploma in Electrical Engineering (Power)', 'Kulim', 'Diploma', '2026-09-21 06:55:41'),
(109, 16, 'Diploma in Electronic Engineering (Medical)', 'Kulim', 'Diploma', '2026-09-21 06:55:41'),
(110, 16, 'Diploma in Mechanical Engineering', 'Kulim', 'Diploma', '2026-09-21 06:55:41'),
(111, 16, 'Diploma in Mechatronics', 'Kulim', 'Diploma', '2026-09-21 06:55:41'),
(112, 16, 'Diploma in Civil Engineering & IT', 'Kulim', 'Diploma', '2026-09-21 06:55:41'),
(113, 17, 'Sijil Pengembaraan, Pelancongan & Operasi Bot', 'Langkawi Tourism Academy', 'Certificate', '2026-09-21 06:55:41'),
(114, 17, 'Sijil Penyelenggaraan Elektrik & Automotif', 'Bandar Darulaman / Sg Petani / Kulim', 'Certificate', '2026-09-21 06:55:41'),
(115, 17, 'Sijil Herba & Pertanian Moden', 'Padang Terap / Sik / Pendang', 'Certificate', '2026-09-21 06:55:41'),
(116, 18, 'Sijil Teknologi Hospitaliti & Penyediaan Makanan', 'Jitra', 'Certificate', '2026-09-21 06:55:41'),
(117, 18, 'Diploma Teknologi Hospitaliti & Penyediaan Makanan', 'Jitra', 'Diploma', '2026-09-21 06:55:41'),
(118, 18, 'Sijil Teknologi Jahitan & Perekaan Fesyen', 'Jitra', 'Certificate', '2026-09-21 06:55:41'),
(119, 18, 'Diploma Teknologi Jahitan & Perekaan Fesyen', 'Jitra', 'Diploma', '2026-09-21 06:55:41'),
(120, 18, 'Sijil Teknologi Elektrik, HVAC & Automotif', 'Jitra', 'Certificate', '2026-09-21 06:55:41'),
(121, 19, 'Sijil Teknologi Pertanian Moden (Agrotechnology)', 'Naka, Pokok Sena', 'Certificate', '2026-09-21 06:55:41'),
(122, 19, 'Diploma Teknologi Pertanian Moden (Agrotechnology)', 'Naka, Pokok Sena', 'Diploma', '2026-09-21 06:55:41'),
(123, 19, 'Sijil Teknologi Mekanikal, Kimpalan & Elektrik', 'Naka, Pokok Sena', 'Certificate', '2026-09-21 06:55:41'),
(124, 20, 'Diploma Teknologi Mikroelektronik', 'Kulim Hi-Tech', 'Diploma', '2026-09-21 06:55:41'),
(125, 20, 'Diploma Teknologi Telekomunikasi', 'Kulim Hi-Tech', 'Diploma', '2026-09-21 06:55:41'),
(126, 20, 'Diploma Teknologi Mekatronik', 'Kulim Hi-Tech', 'Diploma', '2026-09-21 06:55:41'),
(127, 20, 'Diploma Teknologi Pengeluaran Automasi & QA', 'Kulim Hi-Tech', 'Diploma', '2026-09-21 06:55:41'),
(128, 21, 'Sijil Teknologi Komputer (Rangkaian)', 'Jitra', 'Certificate', '2026-09-21 06:55:41'),
(129, 21, 'DKM Teknologi Komputer (Rangkaian)', 'Jitra', 'Diploma', '2026-09-21 06:55:41'),
(130, 21, 'Sijil Teknologi Rekabentuk CAD/CAM, Elektrik & Kimpalan', 'Jitra', 'Certificate', '2026-09-21 06:55:41'),
(131, 22, 'Sijil Teknologi Penyelenggaraan Loji Industri', 'Sungai Petani', 'Certificate', '2026-09-21 06:55:41'),
(132, 22, 'Diploma Teknologi Penyelenggaraan Loji Industri', 'Sungai Petani', 'Diploma', '2026-09-21 06:55:41'),
(133, 22, 'Sijil Lukisan Senibina', 'Sungai Petani', 'Certificate', '2026-09-21 06:55:41'),
(134, 22, 'Sijil Lukisan Kejuruteraan Awam & Elektrik', 'Sungai Petani', 'Certificate', '2026-09-21 06:55:41'),
(135, 23, 'Sijil Teknologi Pembuatan Perabot & Hiasan Dalaman', 'Sik', 'Certificate', '2026-09-21 06:55:41'),
(136, 23, 'Diploma Teknologi Pembuatan Perabot & Hiasan Dalaman', 'Sik', 'Diploma', '2026-09-21 06:55:41'),
(137, 23, 'Sijil Teknologi Kimpalan & Fabrikasi', 'Sik', 'Certificate', '2026-09-21 06:55:41'),
(138, 24, 'SVM Teknologi Automotif & HVAC', 'Alor Setar / Sungai Petani 1 & 2', 'SVM/DVM', '2026-09-21 06:55:41'),
(139, 24, 'DVM Teknologi Automotif & HVAC', 'Alor Setar / Sungai Petani 1 & 2', 'SVM/DVM', '2026-09-21 06:55:41'),
(140, 24, 'SVM Teknologi Elektrik & Kimpalan', 'Kulim / Alor Setar', 'SVM/DVM', '2026-09-21 06:55:41'),
(141, 24, 'DVM Teknologi Elektrik & Kimpalan', 'Kulim / Alor Setar', 'SVM/DVM', '2026-09-21 06:55:41'),
(142, 24, 'SVM Pengurusan Pelancongan & Seni Kulinari', 'KV Langkawi', 'SVM/DVM', '2026-09-21 06:55:41'),
(143, 24, 'DVM Pengurusan Pelancongan & Seni Kulinari', 'KV Langkawi', 'SVM/DVM', '2026-09-21 06:55:41'),
(144, 25, 'Diploma in Computer Engineering', 'Arau / Kangar', 'Diploma', '2026-09-21 06:55:41'),
(145, 25, 'Diploma in Electrical Engineering', 'Arau / Kangar', 'Diploma', '2026-09-21 06:55:41'),
(146, 25, 'Diploma in Microelectronic Engineering', 'Arau / Kangar', 'Diploma', '2026-09-21 06:55:41'),
(147, 25, 'Diploma in Mechanical Engineering', 'Arau / Kangar', 'Diploma', '2026-09-21 06:55:41'),
(148, 25, 'B.Eng. Microelectronic Engineering', 'Arau / Kangar', 'Degree', '2026-09-21 06:55:41'),
(149, 25, 'B.Eng. Electronic Systems Engineering', 'Arau / Kangar', 'Degree', '2026-09-21 06:55:41'),
(150, 25, 'B.Eng. Computer Engineering', 'Arau / Kangar', 'Degree', '2026-09-21 06:55:41'),
(151, 25, 'B.Eng. Cyber Security Engineering', 'Arau / Kangar', 'Degree', '2026-09-21 06:55:41'),
(152, 25, 'B.Eng. Mechatronic & Biomedical Engineering', 'Arau / Kangar', 'Degree', '2026-09-21 06:55:41'),
(153, 25, 'B.Eng. Mechanical Engineering', 'Arau / Kangar', 'Degree', '2026-09-21 06:55:41'),
(154, 25, 'B.Eng. Manufacturing Engineering', 'Arau / Kangar', 'Degree', '2026-09-21 06:55:41'),
(155, 25, 'B.Eng. Materials Engineering', 'Arau / Kangar', 'Degree', '2026-09-21 06:55:41'),
(156, 25, 'B.Eng. Civil Engineering', 'Arau / Kangar', 'Degree', '2026-09-21 06:55:41'),
(157, 25, 'B.Tech. Industrial Electronics', 'Arau / Kangar', 'Degree', '2026-09-21 06:55:41'),
(158, 25, 'B.Tech. Biosystems Technology', 'Arau / Kangar', 'Degree', '2026-09-21 06:55:41'),
(159, 26, 'Diploma in Computer Science', 'Arau', 'Diploma', '2026-09-21 06:55:41'),
(160, 26, 'B.Sc. Computer Science', 'Arau', 'Degree', '2026-09-21 06:55:41'),
(161, 26, 'Diploma in Mathematical Sciences', 'Arau', 'Diploma', '2026-09-21 06:55:41'),
(162, 26, 'B.Sc. Mathematical Sciences', 'Arau', 'Degree', '2026-09-21 06:55:41'),
(163, 26, 'Diploma in Geomatics', 'Arau', 'Diploma', '2026-09-21 06:55:41'),
(164, 26, 'Diploma in Surveying Science', 'Arau', 'Diploma', '2026-09-21 06:55:41'),
(165, 26, 'B.Sc. Geomatics', 'Arau', 'Degree', '2026-09-21 06:55:41'),
(166, 26, 'Surveying Science', 'Arau', 'Degree', '2026-09-21 06:55:41'),
(167, 26, 'Diploma in Agrotechnology', 'Arau', 'Diploma', '2026-09-21 06:55:41'),
(168, 26, 'B.Sc. Agrotechnology', 'Arau', 'Degree', '2026-09-21 06:55:41'),
(169, 26, 'Diploma in Sports Science', 'Arau', 'Diploma', '2026-09-21 06:55:41'),
(170, 26, 'Bachelor of Accountancy', 'Arau', 'Degree', '2026-09-21 06:55:41'),
(171, 27, 'Diploma in Electrical & Electronic Engineering', 'Arau (Ulu Pauh)', 'Diploma', '2026-09-21 06:55:41'),
(172, 27, 'Diploma in Information Technology (Digital Technology)', 'Arau (Ulu Pauh)', 'Diploma', '2026-09-21 06:55:41'),
(173, 27, 'Diploma in Information Technology (Cyber Security)', 'Arau (Ulu Pauh)', 'Diploma', '2026-09-21 06:55:41'),
(174, 27, 'Diploma in Design & New Media (Graphic Design)', 'Arau (Ulu Pauh)', 'Diploma', '2026-09-21 06:55:41'),
(175, 27, 'Diploma in Design & New Media (Animation)', 'Arau (Ulu Pauh)', 'Diploma', '2026-09-21 06:55:41'),
(176, 27, 'Diploma in Hotel Management & Foodservice Management', 'Arau (Ulu Pauh)', 'Diploma', '2026-09-21 06:55:41'),
(177, 27, 'Diploma in Tourism Management', 'Arau (Ulu Pauh)', 'Diploma', '2026-09-21 06:55:41'),
(178, 27, 'Diploma in International Business', 'Arau (Ulu Pauh)', 'Diploma', '2026-09-21 06:55:41'),
(179, 28, 'Sijil Teknologi Maklumat (Pengoperasian Rangkaian)', 'Arau / Kangar', 'Certificate', '2026-09-21 06:55:41'),
(180, 28, 'Sijil Penyelenggaraan Elektrik, Fashion & Kulinari', 'Arau / Kangar', 'Certificate', '2026-09-21 06:55:41'),
(181, 29, 'Sijil Teknologi Marin (Enjin Bot & Kapal)', 'Kuala Perlis', 'Certificate', '2026-09-21 06:55:41'),
(182, 29, 'Diploma Teknologi Marin (Enjin Bot & Kapal)', 'Kuala Perlis', 'Diploma', '2026-09-21 06:55:41'),
(183, 29, 'Sijil Teknologi Air-Conditioning Marin', 'Kuala Perlis', 'Certificate', '2026-09-21 06:55:41'),
(184, 29, 'Diploma Teknologi Air-Conditioning Marin', 'Kuala Perlis', 'Diploma', '2026-09-21 06:55:41'),
(185, 29, 'Sijil Teknologi Automotif & Penyediaan Makanan', 'Kuala Perlis', 'Certificate', '2026-09-21 06:55:41'),
(186, 30, 'Sijil Teknologi Mekatronik & Elektronik Industri', 'Kangar', 'Certificate', '2026-09-21 06:55:41'),
(187, 30, 'Diploma Teknologi Mekatronik & Elektronik Industri', 'Kangar', 'Diploma', '2026-09-21 06:55:41'),
(188, 30, 'Sijil Pemesinan CNC, Rangkaian Komputer & Kimpalan', 'Kangar', 'Certificate', '2026-09-21 06:55:41'),
(189, 31, 'Sijil Teknologi Kejuruteraan Marin', 'Beseri', 'Certificate', '2026-09-21 06:55:41'),
(190, 31, 'Diploma Teknologi Kejuruteraan Marin', 'Beseri', 'Diploma', '2026-09-21 06:55:41'),
(191, 31, 'Sijil Penyelenggaraan Automotif, Kimpalan & Pembinaan Bangunan', 'Beseri', 'Certificate', '2026-09-21 06:55:41'),
(192, 32, 'SVM Teknologi Elektronik, Elektrik & Automotif', 'KV Kangar / KV Arau', 'SVM/DVM', '2026-09-21 06:55:41'),
(193, 32, 'DVM Teknologi Elektronik, Elektrik & Automotif', 'KV Kangar / KV Arau', 'SVM/DVM', '2026-09-21 06:55:41'),
(194, 32, 'SVM Kimpalan, Pastri & Pendidikan Awal Kanak-Kanak', 'KV Kangar / KV Arau', 'SVM/DVM', '2026-09-21 06:55:41'),
(195, 32, 'DVM Kimpalan, Pastri & Pendidikan Awal Kanak-Kanak', 'KV Kangar / KV Arau', 'SVM/DVM', '2026-09-21 06:55:41'),
(197, 34, 'Sijil Sistem Komputer', 'Kampus Utama', 'SKM', '2026-10-12 13:45:12'),
(198, 34, 'Sijil Mechatronik', 'Kampus Utama', 'SKM', '2026-10-12 13:57:21');

-- --------------------------------------------------------

--
-- Table structure for table `temp_courses` (and view `temp_Course`)
--

CREATE TABLE `temp_courses` (
  `id` int(11) NOT NULL,
  `institute_id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `campus_branch` varchar(255) NOT NULL DEFAULT 'Kampus Utama',
  `qualification_level` varchar(100) NOT NULL DEFAULT 'Diploma',
  `status` enum('KIV','Approve') NOT NULL DEFAULT 'KIV',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE OR REPLACE VIEW `temp_Course` AS SELECT * FROM `temp_courses`;

-- --------------------------------------------------------

--
-- Table structure for table `industry_job_required_skills`
--

CREATE TABLE `industry_job_required_skills` (
  `id` int(11) NOT NULL,
  `framework_id` int(11) NOT NULL,
  `skill_name` varchar(150) NOT NULL,
  `skill_type` enum('technical','soft') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `industry_job_required_skills`
--

INSERT INTO `industry_job_required_skills` (`id`, `framework_id`, `skill_name`, `skill_type`, `created_at`) VALUES
(1, 1, 'Communication', 'soft', '2026-10-12 08:58:22'),
(2, 1, 'Teamwork', 'soft', '2026-10-12 08:58:22'),
(3, 1, 'Discipline', 'soft', '2026-10-12 08:58:22'),
(4, 1, 'Lifelong Learning', 'soft', '2026-10-12 08:58:22'),
(5, 1, 'Machine Operation', 'technical', '2026-10-12 08:58:22'),
(6, 1, '5S/Cleanroom', 'technical', '2026-10-12 08:58:22'),
(7, 1, 'Safety (OSHA)', 'technical', '2026-10-12 08:58:22'),
(8, 1, 'Quality Basics', 'technical', '2026-10-12 08:58:22'),
(9, 1, 'Good Manufacturing Practices', 'technical', '2026-10-12 08:58:22'),
(10, 2, 'Communication', 'soft', '2026-10-12 08:58:22'),
(11, 2, 'Problem Solving', 'soft', '2026-10-12 08:58:22'),
(12, 2, 'Supervisory Skills', 'soft', '2026-10-12 08:58:22'),
(13, 2, 'Equipment Operation', 'technical', '2026-10-12 08:58:22'),
(14, 2, 'Maintenance Basics', 'technical', '2026-10-12 08:58:22'),
(15, 2, 'Cleanroom/ESD Practices', 'technical', '2026-10-12 08:58:22'),
(16, 2, 'Quality Control', 'technical', '2026-10-12 08:58:22'),
(17, 3, 'Leadership', 'soft', '2026-10-12 08:58:22'),
(18, 3, 'Teamwork', 'soft', '2026-10-12 08:58:22'),
(19, 3, 'Problem Solving', 'soft', '2026-10-12 08:58:22'),
(20, 3, 'Communication', 'soft', '2026-10-12 08:58:22'),
(21, 3, 'Production Coordination', 'technical', '2026-10-12 08:58:22'),
(22, 3, 'Machine Troubleshooting', 'technical', '2026-10-12 08:58:22'),
(23, 3, 'Quality Compliance', 'technical', '2026-10-12 08:58:22'),
(24, 3, 'Reporting', 'technical', '2026-10-12 08:58:22'),
(25, 4, 'Teamwork', 'soft', '2026-10-12 08:58:22'),
(26, 4, 'Communication', 'soft', '2026-10-12 08:58:22'),
(27, 4, 'Problem Solving', 'soft', '2026-10-12 08:58:22'),
(28, 4, 'Equipment Calibration', 'technical', '2026-10-12 08:58:22'),
(29, 4, 'Preventive Maintenance', 'technical', '2026-10-12 08:58:22'),
(30, 4, 'SPC/Minitab', 'technical', '2026-10-12 08:58:22'),
(31, 4, 'Data Collection', 'technical', '2026-10-12 08:58:22'),
(32, 5, 'Time Management', 'soft', '2026-10-12 08:58:22'),
(33, 5, 'Problem Solving', 'soft', '2026-10-12 08:58:22'),
(34, 5, 'Communication', 'soft', '2026-10-12 08:58:22'),
(35, 5, 'PLCs', 'technical', '2026-10-12 08:58:22'),
(36, 5, 'Motors & Control Systems', 'technical', '2026-10-12 08:58:22'),
(37, 5, 'Preventive Maintenance', 'technical', '2026-10-12 08:58:22'),
(38, 5, 'Electrical/Pneumatic Systems', 'technical', '2026-10-12 08:58:22'),
(39, 6, 'Leadership', 'soft', '2026-10-12 08:58:22'),
(40, 6, 'Analytical Thinking', 'soft', '2026-10-12 08:58:22'),
(41, 6, 'Teamwork', 'soft', '2026-10-12 08:58:22'),
(42, 6, 'Advanced Troubleshooting', 'technical', '2026-10-12 08:58:22'),
(43, 6, 'Electrical/Mechanical Diagnostics', 'technical', '2026-10-12 08:58:22'),
(44, 6, 'Quality Improvement', 'technical', '2026-10-12 08:58:22'),
(45, 7, 'Communication', 'soft', '2026-10-12 08:58:22'),
(46, 7, 'Presentation', 'soft', '2026-10-12 08:58:22'),
(47, 7, 'Coaching', 'soft', '2026-10-12 08:58:22'),
(48, 7, 'Teamwork', 'soft', '2026-10-12 08:58:22'),
(49, 7, 'Training Delivery', 'technical', '2026-10-12 08:58:22'),
(50, 7, 'Lean Manufacturing', 'technical', '2026-10-12 08:58:22'),
(51, 7, 'Quality Systems', 'technical', '2026-10-12 08:58:22'),
(52, 7, 'Safety Compliance', 'technical', '2026-10-12 08:58:22'),
(53, 8, 'Problem Solving', 'soft', '2026-10-12 08:58:22'),
(54, 8, 'Attention to Detail', 'soft', '2026-10-12 08:58:22'),
(55, 8, 'Adaptability', 'soft', '2026-10-12 08:58:22'),
(56, 8, 'PLC', 'technical', '2026-10-12 08:58:22'),
(57, 8, 'Ladder Logic', 'technical', '2026-10-12 08:58:22'),
(58, 8, 'C++', 'technical', '2026-10-12 08:58:22'),
(59, 8, 'Python', 'technical', '2026-10-12 08:58:22'),
(60, 8, 'Data Analysis', 'technical', '2026-10-12 08:58:22'),
(61, 8, 'ESD Awareness', 'technical', '2026-10-12 08:58:22'),
(62, 9, 'Analytical Thinking', 'soft', '2026-10-12 08:58:22'),
(63, 9, 'Collaboration', 'soft', '2026-10-12 08:58:22'),
(64, 9, 'Adaptability', 'soft', '2026-10-12 08:58:22'),
(65, 9, 'Robotics', 'technical', '2026-10-12 08:58:22'),
(66, 9, 'AI Tools (Power BI', 'technical', '2026-10-12 08:58:22'),
(67, 9, 'UI Path)', 'technical', '2026-10-12 08:58:22'),
(68, 9, 'Data Analytics', 'technical', '2026-10-12 08:58:22'),
(69, 9, 'Control Systems Integration', 'technical', '2026-10-12 08:58:22'),
(70, 10, 'Leadership', 'soft', '2026-10-12 08:58:22'),
(71, 10, 'Project Management', 'soft', '2026-10-12 08:58:22'),
(72, 10, 'Influencing Skills', 'soft', '2026-10-12 08:58:22'),
(73, 10, 'IoT', 'technical', '2026-10-12 08:58:22'),
(74, 10, 'Embedded Systems', 'technical', '2026-10-12 08:58:22'),
(75, 10, 'FPGA', 'technical', '2026-10-12 08:58:22'),
(76, 10, 'AI Automation', 'technical', '2026-10-12 08:58:22'),
(77, 10, 'Safety & Compliance (LOTO', 'technical', '2026-10-12 08:58:22'),
(78, 10, 'HIRARC)', 'technical', '2026-10-12 08:58:22'),
(79, 11, 'Communication', 'soft', '2026-10-12 08:58:22'),
(80, 11, 'Teamwork', 'soft', '2026-10-12 08:58:22'),
(81, 11, 'Adaptability', 'soft', '2026-10-12 08:58:22'),
(82, 11, 'Building Systems', 'technical', '2026-10-12 08:58:22'),
(83, 11, 'HVAC', 'technical', '2026-10-12 08:58:22'),
(84, 11, 'Plant Utilities Treatment (PUT)', 'technical', '2026-10-12 08:58:22'),
(85, 11, 'AutoCAD', 'technical', '2026-10-12 08:58:22'),
(86, 12, 'Supervisory Skills', 'soft', '2026-10-12 08:58:22'),
(87, 12, 'Conflict Resolution', 'soft', '2026-10-12 08:58:22'),
(88, 12, 'Analytical Thinking', 'soft', '2026-10-12 08:58:22'),
(89, 12, 'HVAC', 'technical', '2026-10-12 08:58:22'),
(90, 12, 'Safety Standards (OSHA', 'technical', '2026-10-12 08:58:22'),
(91, 12, 'UBBL)', 'technical', '2026-10-12 08:58:22'),
(92, 12, 'CAD Design', 'technical', '2026-10-12 08:58:22'),
(93, 12, 'Data Analysis', 'technical', '2026-10-12 08:58:22'),
(94, 13, 'Stakeholder Management', 'soft', '2026-10-12 08:58:22'),
(95, 13, 'Leadership', 'soft', '2026-10-12 08:58:22'),
(96, 13, 'Conflict Resolution', 'soft', '2026-10-12 08:58:22'),
(97, 13, 'Energy Management', 'technical', '2026-10-12 08:58:22'),
(98, 13, 'ESG Practices', 'technical', '2026-10-12 08:58:22'),
(99, 13, 'Project & Vendor Management', 'technical', '2026-10-12 08:58:22'),
(100, 13, 'Safety Compliance', 'technical', '2026-10-12 08:58:22'),
(101, 14, 'Team Collaboration', 'soft', '2026-10-12 08:58:22'),
(102, 14, 'Adaptability', 'soft', '2026-10-12 08:58:22'),
(103, 14, 'Time Management', 'soft', '2026-10-12 08:58:22'),
(104, 14, 'Equipment Setup', 'technical', '2026-10-12 08:58:22'),
(105, 14, 'Preventive Maintenance', 'technical', '2026-10-12 08:58:22'),
(106, 14, 'Data Analytics', 'technical', '2026-10-12 08:58:22'),
(107, 14, 'Safety Awareness', 'technical', '2026-10-12 08:58:22'),
(108, 15, 'Problem Solving', 'soft', '2026-10-12 08:58:22'),
(109, 15, 'Communication', 'soft', '2026-10-12 08:58:22'),
(110, 15, 'Analytical Skills', 'soft', '2026-10-12 08:58:22'),
(111, 15, 'Equipment Calibration', 'technical', '2026-10-12 08:58:22'),
(112, 15, 'DOE', 'technical', '2026-10-12 08:58:22'),
(113, 15, 'Statistical Process Control', 'technical', '2026-10-12 08:58:22'),
(114, 15, 'Supplier Management', 'technical', '2026-10-12 08:58:22'),
(115, 16, 'Leadership', 'soft', '2026-10-12 08:58:22'),
(116, 16, 'Coaching', 'soft', '2026-10-12 08:58:22'),
(117, 16, 'Project Management', 'soft', '2026-10-12 08:58:22'),
(118, 16, 'Advanced Equipment Diagnostics', 'technical', '2026-10-12 08:58:22'),
(119, 16, 'Robotics', 'technical', '2026-10-12 08:58:22'),
(120, 16, 'Automation Integration', 'technical', '2026-10-12 08:58:22'),
(121, 16, 'AI Tools', 'technical', '2026-10-12 08:58:22'),
(122, 17, 'Communication', 'soft', '2026-10-12 08:58:22'),
(123, 17, 'Analytical Thinking', 'soft', '2026-10-12 08:58:22'),
(124, 17, 'Process Control', 'technical', '2026-10-12 08:58:22'),
(125, 17, 'Data Analysis (SPC)', 'technical', '2026-10-12 08:58:22'),
(126, 17, 'DOE', 'technical', '2026-10-12 08:58:22'),
(127, 18, 'Problem Solving', 'soft', '2026-10-12 08:58:22'),
(128, 18, 'Collaboration', 'soft', '2026-10-12 08:58:22'),
(129, 18, 'SPC', 'technical', '2026-10-12 08:58:22'),
(130, 18, 'FMEA', 'technical', '2026-10-12 08:58:22'),
(131, 18, 'Process Mapping', 'technical', '2026-10-12 08:58:22'),
(132, 18, 'Continuous Improvement', 'technical', '2026-10-12 08:58:22'),
(133, 19, 'Leadership', 'soft', '2026-10-12 08:58:22'),
(134, 19, 'Analytical Thinking', 'soft', '2026-10-12 08:58:22'),
(135, 19, 'Advanced DOE', 'technical', '2026-10-12 08:58:22'),
(136, 19, 'Six Sigma', 'technical', '2026-10-12 08:58:22'),
(137, 19, 'AI-based Process Optimisation', 'technical', '2026-10-12 08:58:22'),
(138, 20, 'Teamwork', 'soft', '2026-10-12 08:58:22'),
(139, 20, 'Communication', 'soft', '2026-10-12 08:58:22'),
(140, 20, 'ISO9001 Awareness', 'technical', '2026-10-12 08:58:22'),
(141, 20, 'Quality Control Tools', 'technical', '2026-10-12 08:58:22'),
(142, 21, 'Problem Solving', 'soft', '2026-10-12 08:58:22'),
(143, 21, 'Analytical Thinking', 'soft', '2026-10-12 08:58:22'),
(144, 21, 'APQP', 'technical', '2026-10-12 08:58:22'),
(145, 21, 'PPAP', 'technical', '2026-10-12 08:58:22'),
(146, 21, 'FMEA', 'technical', '2026-10-12 08:58:22'),
(147, 21, 'SPC', 'technical', '2026-10-12 08:58:22'),
(148, 21, 'MSA', 'technical', '2026-10-12 08:58:22'),
(149, 22, 'Leadership', 'soft', '2026-10-12 08:58:22'),
(150, 22, 'Communication', 'soft', '2026-10-12 08:58:22'),
(151, 22, 'Advanced Statistical Methods', 'technical', '2026-10-12 08:58:22'),
(152, 22, 'Lean Six Sigma', 'technical', '2026-10-12 08:58:22'),
(153, 22, 'Risk Management', 'technical', '2026-10-12 08:58:22'),
(154, 23, 'Attention to Detail', 'soft', '2026-10-12 08:58:22'),
(155, 23, 'Problem Solving', 'soft', '2026-10-12 08:58:22'),
(156, 23, 'Test Equipment Setup', 'technical', '2026-10-12 08:58:22'),
(157, 23, 'Data Analysis', 'technical', '2026-10-12 08:58:22'),
(158, 24, 'Communication', 'soft', '2026-10-12 08:58:22'),
(159, 24, 'Collaboration', 'soft', '2026-10-12 08:58:22'),
(160, 24, 'Test Automation', 'technical', '2026-10-12 08:58:22'),
(161, 24, 'Data Interpretation', 'technical', '2026-10-12 08:58:22'),
(162, 24, 'Statistical Tools', 'technical', '2026-10-12 08:58:22'),
(163, 25, 'Leadership', 'soft', '2026-10-12 08:58:22'),
(164, 25, 'Analytical Skills', 'soft', '2026-10-12 08:58:22'),
(165, 25, 'Advanced Test Automation', 'technical', '2026-10-12 08:58:22'),
(166, 25, 'Data Analytics', 'technical', '2026-10-12 08:58:22'),
(167, 25, 'Quality Tools (FMEA', 'technical', '2026-10-12 08:58:22'),
(168, 25, 'SPC)', 'technical', '2026-10-12 08:58:22'),
(169, 26, 'Project Management', 'soft', '2026-10-12 08:58:22'),
(170, 26, 'Teamwork', 'soft', '2026-10-12 08:58:22'),
(171, 26, 'Product Qualification', 'technical', '2026-10-12 08:58:22'),
(172, 26, 'Data Correlation', 'technical', '2026-10-12 08:58:22'),
(173, 26, 'Design Validation', 'technical', '2026-10-12 08:58:22'),
(174, 27, 'Leadership', 'soft', '2026-10-12 08:58:22'),
(175, 27, 'Stakeholder Management', 'soft', '2026-10-12 08:58:22'),
(176, 27, 'Product Lifecycle Management', 'technical', '2026-10-12 08:58:22'),
(177, 27, 'DFM', 'technical', '2026-10-12 08:58:22'),
(178, 27, 'FMEA', 'technical', '2026-10-12 08:58:22'),
(179, 27, 'Reliability Testing', 'technical', '2026-10-12 08:58:22'),
(180, 28, 'Communication', 'soft', '2026-10-12 08:58:22'),
(181, 28, 'Teamwork', 'soft', '2026-10-12 08:58:22'),
(182, 28, 'Adaptability', 'soft', '2026-10-12 08:58:22'),
(183, 28, 'NPI Process Flow', 'technical', '2026-10-12 08:58:22'),
(184, 28, 'Product Qualification', 'technical', '2026-10-12 08:58:22'),
(185, 28, 'Data Correlation', 'technical', '2026-10-12 08:58:22'),
(186, 28, 'DOE', 'technical', '2026-10-12 08:58:22'),
(187, 29, 'Project Management', 'soft', '2026-10-12 08:58:22'),
(188, 29, 'Problem Solving', 'soft', '2026-10-12 08:58:22'),
(189, 29, 'Design of Experiments (DOE)', 'technical', '2026-10-12 08:58:22'),
(190, 29, 'FMEA', 'technical', '2026-10-12 08:58:22'),
(191, 29, 'Process Optimisation', 'technical', '2026-10-12 08:58:22'),
(192, 29, 'Product Validation', 'technical', '2026-10-12 08:58:22'),
(193, 30, 'Leadership', 'soft', '2026-10-12 08:58:22'),
(194, 30, 'Stakeholder Management', 'soft', '2026-10-12 08:58:22'),
(195, 30, 'Analytical Thinking', 'soft', '2026-10-12 08:58:22'),
(196, 30, 'DFM/DFA', 'technical', '2026-10-12 08:58:22'),
(197, 30, 'Reliability Testing', 'technical', '2026-10-12 08:58:22'),
(198, 30, 'Failure Analysis', 'technical', '2026-10-12 08:58:22'),
(199, 30, 'Product Lifecycle Management', 'technical', '2026-10-12 08:58:22'),
(200, 31, 'Analytical Thinking', 'soft', '2026-10-12 08:58:22'),
(201, 31, 'Communication', 'soft', '2026-10-12 08:58:22'),
(202, 31, 'Package Design', 'technical', '2026-10-12 08:58:22'),
(203, 31, 'CAD Layout', 'technical', '2026-10-12 08:58:22'),
(204, 31, 'Material Characterisation', 'technical', '2026-10-12 08:58:22'),
(205, 32, 'Problem Solving', 'soft', '2026-10-12 08:58:22'),
(206, 32, 'Collaboration', 'soft', '2026-10-12 08:58:22'),
(207, 32, 'Package Modelling (ANSYS', 'technical', '2026-10-12 08:58:22'),
(208, 32, 'SolidWorks)', 'technical', '2026-10-12 08:58:22'),
(209, 32, 'Thermal Analysis', 'technical', '2026-10-12 08:58:22'),
(210, 32, 'FEA', 'technical', '2026-10-12 08:58:22'),
(211, 32, 'DOE', 'technical', '2026-10-12 08:58:22'),
(212, 33, 'Leadership', 'soft', '2026-10-12 08:58:22'),
(213, 33, 'Project Management', 'soft', '2026-10-12 08:58:22'),
(214, 33, 'Advanced Packaging', 'technical', '2026-10-12 08:58:22'),
(215, 33, 'Flip Chip', 'technical', '2026-10-12 08:58:22'),
(216, 33, 'TSV', 'technical', '2026-10-12 08:58:22'),
(217, 33, 'Process Simulation', 'technical', '2026-10-12 08:58:22'),
(218, 33, 'Reliability Validation', 'technical', '2026-10-12 08:58:22'),
(219, 34, 'Teamwork', 'soft', '2026-10-12 08:58:22'),
(220, 34, 'Analytical Thinking', 'soft', '2026-10-12 08:58:22'),
(221, 34, 'Test Program Debugging', 'technical', '2026-10-12 08:58:22'),
(222, 34, 'Test Fixture Design', 'technical', '2026-10-12 08:58:22'),
(223, 34, 'LabView/Python Scripting', 'technical', '2026-10-12 08:58:22'),
(224, 35, 'Communication', 'soft', '2026-10-12 08:58:22'),
(225, 35, 'Problem Solving', 'soft', '2026-10-12 08:58:22'),
(226, 35, 'ATE Systems (Teradyne', 'technical', '2026-10-12 08:58:22'),
(227, 35, 'Advantest)', 'technical', '2026-10-12 08:58:22'),
(228, 35, 'Test Data Analytics', 'technical', '2026-10-12 08:58:22'),
(229, 35, 'Correlation Studies', 'technical', '2026-10-12 08:58:22'),
(230, 36, 'Leadership', 'soft', '2026-10-12 08:58:22'),
(231, 36, 'Collaboration', 'soft', '2026-10-12 08:58:22'),
(232, 36, 'Analytical Thinking', 'soft', '2026-10-12 08:58:22'),
(233, 36, 'Test Methodology Design', 'technical', '2026-10-12 08:58:22'),
(234, 36, 'Data Automation', 'technical', '2026-10-12 08:58:22'),
(235, 36, 'Yield Analysis', 'technical', '2026-10-12 08:58:22'),
(236, 36, 'Reliability Testing', 'technical', '2026-10-12 08:58:22');

-- --------------------------------------------------------

--
-- Table structure for table `industry_skills_framework`
--

CREATE TABLE `industry_skills_framework` (
  `id` int(11) NOT NULL,
  `focus_area` varchar(100) NOT NULL,
  `job_area` varchar(150) NOT NULL,
  `job_position` varchar(150) NOT NULL,
  `job_description` text NOT NULL,
  `soft_skills_raw` text NOT NULL,
  `technical_skills_raw` text NOT NULL,
  `total_soft_skills` int(11) DEFAULT 0,
  `total_technical_skills` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `industry_skills_framework`
--

INSERT INTO `industry_skills_framework` (`id`, `focus_area`, `job_area`, `job_position`, `job_description`, `soft_skills_raw`, `technical_skills_raw`, `total_soft_skills`, `total_technical_skills`, `created_at`) VALUES
(1, 'Technical', 'Manufacturing Operations', 'Operator', 'Operates machines and performs housekeeping in cleanroom,follows SOPs.', 'Communication, Teamwork, Discipline, Lifelong Learning', 'Machine Operation, 5S/Cleanroom, Safety (OSHA), Quality Basics, Good Manufacturing Practices', 4, 5, '2026-10-12 08:58:22'),
(2, 'Technical', 'Manufacturing Operations', 'Senior Operator', 'Oversees production lines; trains junior operators; ensures compliance.', 'Communication, Problem Solving, Supervisory Skills', 'Equipment Operation, Maintenance Basics, Cleanroom/ESD Practices, Quality Control', 3, 4, '2026-10-12 08:58:22'),
(3, 'Technical', 'Manufacturing Operations', 'Line Leader', 'Leads team of operators, ensures productivity, resolves process issues.', 'Leadership, Teamwork, Problem Solving, Communication', 'Production Coordination, Machine Troubleshooting, Quality Compliance, Reporting', 4, 4, '2026-10-12 08:58:22'),
(4, 'Technical', 'Manufacturing Operations', 'Technical Assistant', 'Supports engineers; sets up and maintains equipment, assists in process monitoring.', 'Teamwork, Communication, Problem Solving', 'Equipment Calibration, Preventive Maintenance, SPC/Minitab, Data Collection', 3, 4, '2026-10-12 08:58:22'),
(5, 'Technical', 'Manufacturing Operations', 'Technician', 'Performs maintenance, repair and equipment setup for production.', 'Time Management, Problem Solving, Communication', 'PLCs, Motors & Control Systems, Preventive Maintenance, Electrical/Pneumatic Systems', 3, 4, '2026-10-12 08:58:22'),
(6, 'Technical', 'Manufacturing Operations', 'Senior Technician', 'Supervises maintenance team; leads troubleshooting; mentors junior techs.', 'Leadership, Analytical Thinking, Teamwork', 'Advanced Troubleshooting, Electrical/Mechanical Diagnostics, Quality Improvement', 3, 3, '2026-10-12 08:58:22'),
(7, 'Technical', 'Manufacturing Operations', 'Manufacturing Trainer', 'Designs and delivers internal manufacturing training programmes.', 'Communication, Presentation, Coaching, Teamwork', 'Training Delivery, Lean Manufacturing, Quality Systems, Safety Compliance', 4, 4, '2026-10-12 08:58:22'),
(8, 'Engineering', 'Factory Automation', 'Assistant Factory Automation Engineer', 'Supports design and maintenance of automation systems; assists with PLC and control system programming.', 'Problem Solving, Attention to Detail, Adaptability', 'PLC, Ladder Logic, C++, Python, Data Analysis, ESD Awareness', 3, 6, '2026-10-12 08:58:22'),
(9, 'Engineering', 'Factory Automation', 'Factory Automation Engineer', 'Designs, implements, and maintains automated systems for process efficiency.', 'Analytical Thinking, Collaboration, Adaptability', 'Robotics, AI Tools (Power BI, UI Path), Data Analytics, Control Systems Integration', 3, 5, '2026-10-12 08:58:22'),
(10, 'Engineering', 'Factory Automation', 'Senior Factory Automation Engineer', 'Leads automation strategy; integrates robotics, IoT and control systems; mentors team.', 'Leadership, Project Management, Influencing Skills', 'IoT, Embedded Systems, FPGA, AI Automation, Safety & Compliance (LOTO, HIRARC)', 3, 6, '2026-10-12 08:58:22'),
(11, 'Engineering', 'Facilities', 'Assistant Facilities Engineer', 'Supports maintenance of utilities (HVAC, electrical, plumbing) and infrastructure systems.', 'Communication, Teamwork, Adaptability', 'Building Systems, HVAC, Plant Utilities Treatment (PUT), AutoCAD', 3, 4, '2026-10-12 08:58:22'),
(12, 'Engineering', 'Facilities', 'Facilities Engineer', 'Oversees building systems and utilities; ensures operational reliability and compliance.', 'Supervisory Skills, Conflict Resolution, Analytical Thinking', 'HVAC, Safety Standards (OSHA, UBBL), CAD Design, Data Analysis', 3, 5, '2026-10-12 08:58:22'),
(13, 'Engineering', 'Facilities', 'Senior Facilities Engineer', 'Leads large-scale facility upgrades, energy conservation and ESG initiatives.', 'Stakeholder Management, Leadership, Conflict Resolution', 'Energy Management, ESG Practices, Project & Vendor Management, Safety Compliance', 3, 4, '2026-10-12 08:58:22'),
(14, 'Engineering', 'Equipment', 'Assistant Equipment Engineer', 'Assists in equipment installation, preventive maintenance and troubleshooting.', 'Team Collaboration, Adaptability, Time Management', 'Equipment Setup, Preventive Maintenance, Data Analytics, Safety Awareness', 3, 4, '2026-10-12 08:58:22'),
(15, 'Engineering', 'Equipment', 'Equipment Engineer', 'Manages equipment performance and reliability; leads technical support to production.', 'Problem Solving, Communication, Analytical Skills', 'Equipment Calibration, DOE, Statistical Process Control, Supplier Management', 3, 4, '2026-10-12 08:58:22'),
(16, 'Engineering', 'Equipment', 'Senior Equipment Engineer', 'Leads equipment improvement projects and advanced troubleshooting.', 'Leadership, Coaching, Project Management', 'Advanced Equipment Diagnostics, Robotics, Automation Integration, AI Tools', 3, 4, '2026-10-12 08:58:22'),
(17, 'Engineering', 'Process', 'Assistant Process Engineer', 'Supports process setup and optimisation, data collection, yield tracking.', 'Communication, Analytical Thinking', 'Process Control, Data Analysis (SPC), DOE', 2, 3, '2026-10-12 08:58:22'),
(18, 'Engineering', 'Process', 'Process Engineer', 'Develops and improves process parameters; ensures yield and quality consistency.', 'Problem Solving, Collaboration', 'SPC, FMEA, Process Mapping, Continuous Improvement', 2, 4, '2026-10-12 08:58:22'),
(19, 'Engineering', 'Process', 'Senior Process Engineer', 'Leads process optimisation projects and cross-departmental improvement initiatives.', 'Leadership, Analytical Thinking', 'Advanced DOE, Six Sigma, AI-based Process Optimisation', 2, 3, '2026-10-12 08:58:22'),
(20, 'Engineering', 'Quality', 'Assistant Quality Engineer', 'Supports quality inspection, control documentation, and audits.', 'Teamwork, Communication', 'ISO9001 Awareness, Quality Control Tools', 2, 2, '2026-10-12 08:58:22'),
(21, 'Engineering', 'Quality', 'Quality Engineer', 'Manages quality systems, root-cause analysis, and customer audit responses.', 'Problem Solving, Analytical Thinking', 'APQP, PPAP, FMEA, SPC, MSA', 2, 5, '2026-10-12 08:58:22'),
(22, 'Engineering', 'Quality', 'Senior Quality Engineer', 'Leads quality strategy, drives process reliability and compliance programs.', 'Leadership, Communication', 'Advanced Statistical Methods, Lean Six Sigma, Risk Management', 2, 3, '2026-10-12 08:58:22'),
(23, 'Engineering', 'Test', 'Assistant Test Engineer', 'Conducts test setup and validation for semiconductor products.', 'Attention to Detail, Problem Solving', 'Test Equipment Setup, Data Analysis', 2, 2, '2026-10-12 08:58:22'),
(24, 'Engineering', 'Test', 'Test Engineer', 'Develops test methods, analyses test data, improves efficiency.', 'Communication, Collaboration', 'Test Automation, Data Interpretation, Statistical Tools', 2, 3, '2026-10-12 08:58:22'),
(25, 'Engineering', 'Test', 'Senior Test Engineer', 'Leads test development strategy, interfaces with design and product teams.', 'Leadership, Analytical Skills', 'Advanced Test Automation, Data Analytics, Quality Tools (FMEA, SPC)', 2, 4, '2026-10-12 08:58:22'),
(26, 'Engineering', 'Product', 'Product Engineer', 'Supports production transfer, product qualification and design validation.', 'Project Management, Teamwork', 'Product Qualification, Data Correlation, Design Validation', 2, 3, '2026-10-12 08:58:22'),
(27, 'Engineering', 'Product', 'Senior Product Engineer', 'Leads product development, integrates design and manufacturing improvements.', 'Leadership, Stakeholder Management', 'Product Lifecycle Management, DFM, FMEA, Reliability Testing', 2, 4, '2026-10-12 08:58:22'),
(28, 'Engineering', 'NPI / Integration', 'Assistant NPI Engineer', 'Supports new product introduction builds (EVT/DVT); coordinates with process and quality teams.', 'Communication, Teamwork, Adaptability', 'NPI Process Flow, Product Qualification, Data Correlation, DOE', 3, 4, '2026-10-12 08:58:22'),
(29, 'Engineering', 'NPI / Integration', 'NPI Engineer', 'Manages product ramp-up and ensures seamless transfer to production.', 'Project Management, Problem Solving', 'Design of Experiments (DOE), FMEA, Process Optimisation, Product Validation', 2, 4, '2026-10-12 08:58:22'),
(30, 'Engineering', 'NPI / Integration', 'Senior NPI / Integration Engineer', 'Leads integration projects; drives manufacturability, yield and performance improvements.', 'Leadership, Stakeholder Management, Analytical Thinking', 'DFM/DFA, Reliability Testing, Failure Analysis, Product Lifecycle Management', 3, 4, '2026-10-12 08:58:22'),
(31, 'Engineering', 'Package / Product Development (PDD)', 'Assistant Packaging Engineer', 'Supports package design and assembly process optimisation.', 'Analytical Thinking, Communication', 'Package Design, CAD Layout, Material Characterisation', 2, 3, '2026-10-12 08:58:22'),
(32, 'Engineering', 'Package / Product Development (PDD)', 'Packaging Engineer', 'Designs new semiconductor package types; coordinates cross-functional teams.', 'Problem Solving, Collaboration', 'Package Modelling (ANSYS, SolidWorks), Thermal Analysis, FEA, DOE', 2, 5, '2026-10-12 08:58:22'),
(33, 'Engineering', 'Package / Product Development (PDD)', 'Senior Packaging Engineer', 'Leads advanced packaging and 3D/SiP innovations.', 'Leadership, Project Management', 'Advanced Packaging, Flip Chip, TSV, Process Simulation, Reliability Validation', 2, 5, '2026-10-12 08:58:22'),
(34, 'Engineering', 'Test Development & Integration (TDI)', 'Assistant TDI Engineer', 'Supports test hardware/software development and validation for new products.', 'Teamwork, Analytical Thinking', 'Test Program Debugging, Test Fixture Design, LabView/Python Scripting', 2, 3, '2026-10-12 08:58:22'),
(35, 'Engineering', 'Test Development & Integration (TDI)', 'TDI Engineer', 'Develops test solutions for IC validation; integrates ATE systems with product specs.', 'Communication, Problem Solving', 'ATE Systems (Teradyne, Advantest), Test Data Analytics, Correlation Studies', 2, 4, '2026-10-12 08:58:22'),
(36, 'Engineering', 'Test Development & Integration (TDI)', 'Senior TDI Engineer', 'Leads cross-functional test integration; drives yield improvement and failure analysis.', 'Leadership, Collaboration, Analytical Thinking', 'Test Methodology Design, Data Automation, Yield Analysis, Reliability Testing', 3, 4, '2026-10-12 08:58:22');

-- --------------------------------------------------------

--
-- Table structure for table `institutes`
--

CREATE TABLE `institutes` (
  `id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `institute_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `institutes`
--

INSERT INTO `institutes` (`id`, `state_id`, `institute_name`, `created_at`) VALUES
(1, 1, 'Universiti Sains Malaysia (USM)', '2026-09-21 06:55:41'),
(2, 1, 'Universiti Teknologi MARA (UiTM)', '2026-09-21 06:55:41'),
(3, 1, 'Politeknik Seberang Perai (PSP)', '2026-09-21 06:55:41'),
(4, 1, 'Politeknik Balik Pulau (PBU)', '2026-09-21 06:55:41'),
(5, 1, 'Kolej Komuniti Penang', '2026-09-21 06:55:41'),
(6, 1, 'IKTBN Bukit Mertajam', '2026-09-21 06:55:41'),
(7, 1, 'ILP Kepala Batas', '2026-09-21 06:55:41'),
(8, 1, 'ILP Perai', '2026-09-21 06:55:41'),
(9, 1, 'IKM Seberang Perai', '2026-09-21 06:55:41'),
(10, 1, 'Kolej Vokasional Penang', '2026-09-21 06:55:41'),
(11, 1, 'Penang Skills Development Centre (PSDC)', '2026-09-21 06:55:41'),
(12, 2, 'Universiti Utara Malaysia (UUM)', '2026-09-21 06:55:41'),
(13, 2, 'UiTM Cawangan Kedah', '2026-09-21 06:55:41'),
(14, 2, 'UniSHAMS', '2026-09-21 06:55:41'),
(15, 2, 'POLIMAS', '2026-09-21 06:55:41'),
(16, 2, 'Politeknik Tuanku Sultanah Bahiyah (PTSB)', '2026-09-21 06:55:41'),
(17, 2, 'Kolej Komuniti Kedah', '2026-09-21 06:55:41'),
(18, 2, 'IKBN Jitra', '2026-09-21 06:55:41'),
(19, 2, 'IKBN Naka', '2026-09-21 06:55:41'),
(20, 2, 'ADTEC Kulim', '2026-09-21 06:55:41'),
(21, 2, 'ILP Jitra', '2026-09-21 06:55:41'),
(22, 2, 'IKM Sungai Petani', '2026-09-21 06:55:41'),
(23, 2, 'IKM Sik', '2026-09-21 06:55:41'),
(24, 2, 'Kolej Vokasional Kedah', '2026-09-21 06:55:41'),
(25, 3, 'Universiti Malaysia Perlis (UniMAP)', '2026-09-21 06:55:41'),
(26, 3, 'UiTM Cawangan Perlis', '2026-09-21 06:55:41'),
(27, 3, 'Politeknik Tuanku Syed Sirajuddin (PTSS)', '2026-09-21 06:55:41'),
(28, 3, 'Kolej Komuniti Arau & Kangar', '2026-09-21 06:55:41'),
(29, 3, 'IKBN Kuala Perlis', '2026-09-21 06:55:41'),
(30, 3, 'ILP Kangar', '2026-09-21 06:55:41'),
(31, 3, 'IKM Beseri', '2026-09-21 06:55:41'),
(32, 3, 'Kolej Vokasional Perlis', '2026-09-21 06:55:41'),
(34, 3, 'Kolej Pertanian', '2026-10-12 13:43:04');

-- --------------------------------------------------------

--
-- Table structure for table `temp_institutes` (and view `temp_Institute`)
--

CREATE TABLE `temp_institutes` (
  `id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `institute_name` varchar(255) NOT NULL,
  `status` enum('KIV','Approve') NOT NULL DEFAULT 'KIV',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE OR REPLACE VIEW `temp_Institute` AS SELECT * FROM `temp_institutes`;

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `skill_name` varchar(150) NOT NULL,
  `skill_type` enum('technical','soft') NOT NULL,
  `qualification_level` varchar(50) DEFAULT 'Diploma',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `skill_name`, `skill_type`, `qualification_level`, `created_at`) VALUES
(1, '5S/Cleanroom', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(2, 'Advanced Troubleshooting', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(3, 'AutoCAD', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(4, 'Building Systems', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(5, 'C++', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(6, 'CAD Layout', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(7, 'Cleanroom/ESD Practices', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(8, 'Data Analysis', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(9, 'Data Analysis (SPC)', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(10, 'Data Analytics', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(11, 'Data Collection', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(12, 'Data Correlation', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(13, 'DOE', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(14, 'Electrical/Mechanical Diagnostics', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(15, 'Electrical/Pneumatic Systems', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(16, 'Equipment Calibration', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(17, 'Equipment Operation', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(18, 'Equipment Setup', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(19, 'ESD Awareness', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(20, 'Good Manufacturing Practices', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(21, 'HVAC', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(22, 'ISO9001 Awareness', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(23, 'LabView/Python Scripting', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(24, 'Ladder Logic', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(25, 'Lean Manufacturing', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(26, 'Machine Operation', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(27, 'Machine Troubleshooting', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(28, 'Maintenance Basics', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(29, 'Material Characterisation', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(30, 'Motors & Control Systems', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(31, 'NPI Process Flow', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(32, 'Package Design', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(33, 'Plant Utilities Treatment (PUT)', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(34, 'PLC', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(35, 'Preventive Maintenance', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(36, 'Process Control', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(37, 'Product Qualification', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(38, 'Production Coordination', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(39, 'Python', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(40, 'Quality Basics', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(41, 'Quality Compliance', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(42, 'Quality Control Tools', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(43, 'Quality Improvement', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(44, 'Quality Systems', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(45, 'Reporting', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(46, 'Safety (OSHA)', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(47, 'Safety Awareness', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(48, 'Safety Compliance', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(49, 'SPC/Minitab', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(50, 'Test Equipment Setup', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(51, 'Test Fixture Design', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(52, 'Test Program Debugging', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(53, 'Training Delivery', 'technical', 'Diploma', '2026-09-21 06:55:41'),
(54, 'Adaptability', 'soft', 'Diploma', '2026-09-21 06:55:41'),
(55, 'Analytical Thinking', 'soft', 'Diploma', '2026-09-21 06:55:41'),
(56, 'Attention to Detail', 'soft', 'Diploma', '2026-09-21 06:55:41'),
(57, 'Coaching', 'soft', 'Diploma', '2026-09-21 06:55:41'),
(58, 'Communication', 'soft', 'Diploma', '2026-09-21 06:55:41'),
(59, 'Discipline', 'soft', 'Diploma', '2026-09-21 06:55:41'),
(60, 'Leadership', 'soft', 'Diploma', '2026-09-21 06:55:41'),
(61, 'Lifelong Learning', 'soft', 'Diploma', '2026-09-21 06:55:41'),
(62, 'Presentation', 'soft', 'Diploma', '2026-09-21 06:55:41'),
(63, 'Problem Solving', 'soft', 'Diploma', '2026-09-21 06:55:41'),
(64, 'Supervisory Skills', 'soft', 'Diploma', '2026-09-21 06:55:41'),
(65, 'Team Collaboration', 'soft', 'Diploma', '2026-09-21 06:55:41'),
(66, 'Teamwork', 'soft', 'Diploma', '2026-09-21 06:55:41'),
(67, 'Time Management', 'soft', 'Diploma', '2026-09-21 06:55:41'),
(68, 'Advanced DOE', 'technical', 'Degree', '2026-09-21 06:55:41'),
(69, 'Advanced Equipment Diagnostics', 'technical', 'Degree', '2026-09-21 06:55:41'),
(70, 'Advanced Packaging', 'technical', 'Degree', '2026-09-21 06:55:41'),
(71, 'Advanced Statistical Methods', 'technical', 'Degree', '2026-09-21 06:55:41'),
(72, 'Advanced Test Automation', 'technical', 'Degree', '2026-09-21 06:55:41'),
(73, 'Advantest', 'technical', 'Degree', '2026-09-21 06:55:41'),
(74, 'AI Automation', 'technical', 'Degree', '2026-09-21 06:55:41'),
(75, 'AI Tools', 'technical', 'Degree', '2026-09-21 06:55:41'),
(76, 'AI-based Process Optimisation', 'technical', 'Degree', '2026-09-21 06:55:41'),
(77, 'APQP', 'technical', 'Degree', '2026-09-21 06:55:41'),
(78, 'ATE Systems (Teradyne)', 'technical', 'Degree', '2026-09-21 06:55:41'),
(79, 'Automation Integration', 'technical', 'Degree', '2026-09-21 06:55:41'),
(80, 'C++ Programming', 'technical', 'Degree', '2026-09-21 06:55:41'),
(81, 'CAD Design', 'technical', 'Degree', '2026-09-21 06:55:41'),
(82, 'Continuous Improvement', 'technical', 'Degree', '2026-09-21 06:55:41'),
(83, 'Correlation Studies', 'technical', 'Degree', '2026-09-21 06:55:41'),
(84, 'Data Analytics', 'technical', 'Degree', '2026-09-21 06:55:41'),
(85, 'Data Automation', 'technical', 'Degree', '2026-09-21 06:55:41'),
(86, 'Data Correlation', 'technical', 'Degree', '2026-09-21 06:55:41'),
(87, 'Data Interpretation', 'technical', 'Degree', '2026-09-21 06:55:41'),
(88, 'Design of Experiments (DOE)', 'technical', 'Degree', '2026-09-21 06:55:41'),
(89, 'Design Validation', 'technical', 'Degree', '2026-09-21 06:55:41'),
(90, 'DFM', 'technical', 'Degree', '2026-09-21 06:55:41'),
(91, 'DFM/DFA', 'technical', 'Degree', '2026-09-21 06:55:41'),
(92, 'DOE', 'technical', 'Degree', '2026-09-21 06:55:41'),
(93, 'Embedded Systems', 'technical', 'Degree', '2026-09-21 06:55:41'),
(94, 'Energy Management', 'technical', 'Degree', '2026-09-21 06:55:41'),
(95, 'Equipment Calibration', 'technical', 'Degree', '2026-09-21 06:55:41'),
(96, 'ESG Practices', 'technical', 'Degree', '2026-09-21 06:55:41'),
(97, 'Failure Analysis', 'technical', 'Degree', '2026-09-21 06:55:41'),
(98, 'FEA', 'technical', 'Degree', '2026-09-21 06:55:41'),
(99, 'Flip Chip', 'technical', 'Degree', '2026-09-21 06:55:41'),
(100, 'FMEA', 'technical', 'Degree', '2026-09-21 06:55:41'),
(101, 'FPGA', 'technical', 'Degree', '2026-09-21 06:55:41'),
(102, 'HVAC', 'technical', 'Degree', '2026-09-21 06:55:41'),
(103, 'IoT', 'technical', 'Degree', '2026-09-21 06:55:41'),
(104, 'Lean Six Sigma', 'technical', 'Degree', '2026-09-21 06:55:41'),
(105, 'Package Modelling (ANSYS)', 'technical', 'Degree', '2026-09-21 06:55:41'),
(106, 'Power BI', 'technical', 'Degree', '2026-09-21 06:55:41'),
(107, 'Process Mapping', 'technical', 'Degree', '2026-09-21 06:55:41'),
(108, 'Process Optimisation', 'technical', 'Degree', '2026-09-21 06:55:41'),
(109, 'Process Simulation', 'technical', 'Degree', '2026-09-21 06:55:41'),
(110, 'Product Lifecycle Management', 'technical', 'Degree', '2026-09-21 06:55:41'),
(111, 'Product Qualification', 'technical', 'Degree', '2026-09-21 06:55:41'),
(112, 'Product Validation', 'technical', 'Degree', '2026-09-21 06:55:41'),
(113, 'Production Part Approval Process', 'technical', 'Degree', '2026-09-21 06:55:41'),
(114, 'Project & Vendor Management', 'technical', 'Degree', '2026-09-21 06:55:41'),
(115, 'Python Programming', 'technical', 'Degree', '2026-09-21 06:55:41'),
(116, 'Quality Tools (FMEA)', 'technical', 'Degree', '2026-09-21 06:55:41'),
(117, 'Reliability Testing', 'technical', 'Degree', '2026-09-21 06:55:41'),
(118, 'Risk Management', 'technical', 'Degree', '2026-09-21 06:55:41'),
(119, 'Robotic Process Automation (UI Path)', 'technical', 'Degree', '2026-09-21 06:55:41'),
(120, 'Robotics', 'technical', 'Degree', '2026-09-21 06:55:41'),
(121, 'Safety Compliance', 'technical', 'Degree', '2026-09-21 06:55:41'),
(122, 'Safety Standards (OSHA)', 'technical', 'Degree', '2026-09-21 06:55:41'),
(123, 'Six Sigma', 'technical', 'Degree', '2026-09-21 06:55:41'),
(124, 'SolidWorks', 'technical', 'Degree', '2026-09-21 06:55:41'),
(125, 'SPC', 'technical', 'Degree', '2026-09-21 06:55:41'),
(126, 'Statistical Process Control', 'technical', 'Degree', '2026-09-21 06:55:41'),
(127, 'Statistical Tools', 'technical', 'Degree', '2026-09-21 06:55:41'),
(128, 'Supplier Management', 'technical', 'Degree', '2026-09-21 06:55:41'),
(129, 'Test Automation', 'technical', 'Degree', '2026-09-21 06:55:41'),
(130, 'Test Data Analytics', 'technical', 'Degree', '2026-09-21 06:55:41'),
(131, 'Test Methodology Design', 'technical', 'Degree', '2026-09-21 06:55:41'),
(132, 'Thermal Analysis', 'technical', 'Degree', '2026-09-21 06:55:41'),
(133, 'Through-Silicon Via', 'technical', 'Degree', '2026-09-21 06:55:41'),
(134, 'Yield Analysis', 'technical', 'Degree', '2026-09-21 06:55:41'),
(135, 'Adaptability', 'soft', 'Degree', '2026-09-21 06:55:41'),
(136, 'Analytical Skills', 'soft', 'Degree', '2026-09-21 06:55:41'),
(137, 'Analytical Thinking', 'soft', 'Degree', '2026-09-21 06:55:41'),
(138, 'Coaching', 'soft', 'Degree', '2026-09-21 06:55:41'),
(139, 'Collaboration', 'soft', 'Degree', '2026-09-21 06:55:41'),
(140, 'Communication', 'soft', 'Degree', '2026-09-21 06:55:41'),
(141, 'Conflict Resolution', 'soft', 'Degree', '2026-09-21 06:55:41'),
(142, 'Influencing Skills', 'soft', 'Degree', '2026-09-21 06:55:41'),
(143, 'Leadership', 'soft', 'Degree', '2026-09-21 06:55:41'),
(144, 'Problem Solving', 'soft', 'Degree', '2026-09-21 06:55:41'),
(145, 'Project Management', 'soft', 'Degree', '2026-09-21 06:55:41'),
(146, 'Stakeholder Management', 'soft', 'Degree', '2026-09-21 06:55:41'),
(147, 'Supervisory Skills', 'soft', 'Degree', '2026-09-21 06:55:41'),
(148, 'Teamwork', 'soft', 'Degree', '2026-09-21 06:55:41'),
(157, 'OS installation', 'technical', 'SKM', '2026-10-12 11:09:07'),
(158, 'Read Manual', 'soft', 'SKM', '2026-10-12 11:09:32'),
(159, 'AI', 'technical', 'SKM', '2026-10-12 13:46:59'),
(160, 'Operating Sistem', 'technical', 'SKM', '2026-10-12 13:46:59'),
(161, 'Networking', 'technical', 'SKM', '2026-10-12 13:46:59'),
(162, 'Problem Solving', 'soft', 'SKM', '2026-10-12 13:47:40'),
(163, '5S', 'soft', 'SKM', '2026-10-12 13:47:40'),
(164, 'ISO', 'soft', 'SKM', '2026-10-12 13:47:40'),
(165, 'Elektrikal', 'technical', 'SKM', '2026-10-12 13:58:10'),
(166, 'elektronik', 'technical', 'SKM', '2026-10-12 13:58:10');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` int(11) NOT NULL,
  `state_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `state_name`, `created_at`) VALUES
(1, 'Penang', '2026-09-21 06:55:41'),
(2, 'Kedah', '2026-09-21 06:55:41'),
(3, 'Perlis', '2026-09-21 06:55:41');

-- --------------------------------------------------------

--
-- Table structure for table `survey_submissions`
--

CREATE TABLE `survey_submissions` (
  `id` int(11) NOT NULL,
  `respondent_name` varchar(100) NOT NULL,
  `respondent_email` varchar(255) DEFAULT NULL,
  `respondent_position` varchar(150) DEFAULT NULL,
  `state_id` int(11) NOT NULL,
  `institute_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `annual_intake` int(11) NOT NULL DEFAULT 0,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_submissions`
--

INSERT INTO `survey_submissions` (`id`, `respondent_name`, `respondent_email`, `respondent_position`, `state_id`, `institute_id`, `course_id`, `annual_intake`, `submitted_at`) VALUES
(2, 'kamal harmoni', 'abc@test.com', NULL, 3, 25, 144, 60, '2026-09-21 08:22:32'),
(4, 'adriana', 'adriana@kismec.org.my', NULL, 2, 20, 126, 10, '2026-09-21 08:42:38'),
(5, 'Ahmad Test Intake', 'ahmad.intake@example.com', NULL, 1, 1, 1, 250, '2026-09-22 01:05:39'),
(6, 'hidayah', 'julia.sofea.1402@gmail.com', NULL, 3, 25, 144, 50, '2026-09-22 01:33:24'),
(7, 'hidayat', 'hidayat@kamalharmoni.com', NULL, 2, 20, 124, 66, '2026-10-12 09:19:09'),
(9, 'adriana', 'adriana@as.as', 'Penyelaras', 1, 9, 63, 40, '2026-10-20 09:43:01');

-- --------------------------------------------------------

--
-- Table structure for table `survey_submission_skills`
--

CREATE TABLE `survey_submission_skills` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `skill_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `survey_submission_skills`
--

INSERT INTO `survey_submission_skills` (`id`, `submission_id`, `skill_id`) VALUES
(1, 2, 39),
(2, 2, 38),
(3, 2, 34),
(4, 2, 36),
(5, 2, 56),
(6, 2, 62),
(7, 4, 7),
(8, 4, 12),
(9, 4, 24),
(10, 4, 56),
(11, 4, 62),
(12, 4, 67),
(13, 6, 27),
(14, 6, 30),
(15, 6, 34),
(16, 6, 40),
(17, 6, 62),
(18, 6, 60),
(19, 6, 57),
(20, 7, 54),
(21, 7, 56),
(22, 7, 63),
(23, 7, 34),
(24, 7, 24),
(25, 7, 5),
(26, 7, 39),
(27, 7, 8),
(28, 7, 19),
(29, 9, 2),
(30, 9, 7),
(31, 9, 11),
(32, 9, 18),
(33, 9, 26),
(34, 9, 30),
(35, 9, 38),
(36, 9, 42),
(37, 9, 51),
(38, 9, 55),
(39, 9, 61),
(40, 9, 65),
(41, 9, 62),
(42, 9, 57);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `institute_id` (`institute_id`);

--
-- Indexes for table `temp_courses`
--
ALTER TABLE `temp_courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `institute_id` (`institute_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `industry_job_required_skills`
--
ALTER TABLE `industry_job_required_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_framework_id` (`framework_id`),
  ADD KEY `idx_skill_name` (`skill_name`),
  ADD KEY `idx_skill_type` (`skill_type`);

--
-- Indexes for table `industry_skills_framework`
--
ALTER TABLE `industry_skills_framework`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_job_pos` (`focus_area`,`job_area`,`job_position`);

--
-- Indexes for table `institutes`
--
ALTER TABLE `institutes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `state_id` (`state_id`);

--
-- Indexes for table `temp_institutes`
--
ALTER TABLE `temp_institutes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `state_id` (`state_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `skill_level_unique` (`skill_name`,`qualification_level`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `state_name` (`state_name`);

--
-- Indexes for table `survey_submissions`
--
ALTER TABLE `survey_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `state_id` (`state_id`),
  ADD KEY `institute_id` (`institute_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `survey_submission_skills`
--
ALTER TABLE `survey_submission_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id` (`submission_id`),
  ADD KEY `skill_id` (`skill_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT for table `temp_courses`
--
ALTER TABLE `temp_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `industry_job_required_skills`
--
ALTER TABLE `industry_job_required_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237;

--
-- AUTO_INCREMENT for table `industry_skills_framework`
--
ALTER TABLE `industry_skills_framework`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `institutes`
--
ALTER TABLE `institutes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `temp_institutes`
--
ALTER TABLE `temp_institutes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `survey_submissions`
--
ALTER TABLE `survey_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `survey_submission_skills`
--
ALTER TABLE `survey_submission_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`institute_id`) REFERENCES `institutes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `temp_courses`
--
ALTER TABLE `temp_courses`
  ADD CONSTRAINT `temp_courses_ibfk_1` FOREIGN KEY (`institute_id`) REFERENCES `institutes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `industry_job_required_skills`
--
ALTER TABLE `industry_job_required_skills`
  ADD CONSTRAINT `industry_job_required_skills_ibfk_1` FOREIGN KEY (`framework_id`) REFERENCES `industry_skills_framework` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `institutes`
--
ALTER TABLE `institutes`
  ADD CONSTRAINT `institutes_ibfk_1` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `temp_institutes`
--
ALTER TABLE `temp_institutes`
  ADD CONSTRAINT `temp_institutes_ibfk_1` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `survey_submissions`
--
ALTER TABLE `survey_submissions`
  ADD CONSTRAINT `survey_submissions_ibfk_1` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`),
  ADD CONSTRAINT `survey_submissions_ibfk_2` FOREIGN KEY (`institute_id`) REFERENCES `institutes` (`id`),
  ADD CONSTRAINT `survey_submissions_ibfk_3` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `survey_submission_skills`
--
ALTER TABLE `survey_submission_skills`
  ADD CONSTRAINT `survey_submission_skills_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `survey_submissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `survey_submission_skills_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------
-- Table structure for table `temp_skills`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `temp_skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `skill_name` varchar(255) NOT NULL,
  `skill_type` enum('technical','soft') NOT NULL,
  `skill_description` text NULL,
  `status` enum('KIV','Approve') NOT NULL DEFAULT 'KIV',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
