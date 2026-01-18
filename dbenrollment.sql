-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 27, 2025 at 01:42 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbenrollment`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblcourse`
--

CREATE TABLE `tblcourse` (
  `course_id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_title` varchar(100) NOT NULL,
  `units` int(11) NOT NULL,
  `lecture_hours` int(11) NOT NULL,
  `lab_hours` int(11) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcourse`
--

INSERT INTO `tblcourse` (`course_id`, `course_code`, `course_title`, `units`, `lecture_hours`, `lab_hours`, `dept_id`, `is_deleted`, `deleted_at`) VALUES
(1, 'ITEC 301', 'Advance Programming', 3, 2, 3, 1, 0, NULL),
(2, 'INTE 351', 'Systems Analysis and Design', 3, 3, 0, 1, 0, NULL),
(3, 'COMP 027', 'Mobile Application Development (SMP PLUS)', 3, 2, 3, 1, 0, NULL),
(4, 'COMP 025', 'Project Management', 3, 2, 3, 1, 0, NULL),
(5, 'COMP 019', 'Applications Development and Emerging Technologies', 3, 2, 3, 1, 0, NULL),
(6, 'COMP 018', 'Database Administration', 3, 2, 3, 1, 0, NULL),
(8, 'COMP 017', 'Multimedia', 3, 2, 3, 1, 0, NULL),
(9, 'COMP 015', 'Fundamentals of Research', 3, 3, 0, 1, 0, NULL),
(10, 'COMP 016', 'Web Development', 3, 2, 3, 1, 0, NULL),
(11, 'ITEC 201', 'Practicum 1 (Junior Programmer 1 / Junior Programmer 2 - 300 hours)', 3, 3, 3, 1, 0, NULL),
(12, 'COMP 009', 'Object Oriented Programming', 3, 2, 3, 1, 0, NULL),
(13, 'COMP 010', 'Information Management', 3, 2, 3, 1, 0, NULL),
(14, 'COMP 012', 'Network Administration', 3, 3, 0, 1, 0, NULL),
(15, 'COMP 013', 'Human Computer Interaction', 3, 3, 0, 1, 0, NULL),
(16, 'COMP 014', 'Quantitative Methods with Modeling and Simulation', 3, 2, 3, 1, 0, NULL),
(17, 'COMP 030', 'Business Intelligence', 3, 2, 3, 1, 0, NULL),
(18, 'INTE 403', 'Systems Administration and Maintenance', 3, 2, 3, 1, 0, NULL),
(19, 'PATHFIT 4', 'Physical Activity Towards Health and Fitness 4', 2, 2, 0, 1, 0, NULL),
(20, 'COMP 006', 'Data Structures and Algorithms', 3, 2, 3, 1, 0, NULL),
(21, 'COMP 007', 'Operating Systems', 3, 2, 3, 1, 0, NULL),
(22, 'COMP 008', 'Data Communications and Networking', 3, 2, 3, 1, 0, NULL),
(23, 'COMP 023', 'Social and Professional Issues in Computing', 3, 3, 0, 1, 0, NULL),
(24, 'INTE 201', 'Programming 3 (Structured Programming)', 3, 2, 3, 1, 0, NULL),
(25, 'INTE 202', 'Integrative Programming and Technologies 1', 3, 2, 3, 1, 0, NULL),
(26, 'PATHFIT 3', 'Physical Activity Towards Health and Fitness 3', 2, 2, 0, 1, 0, NULL),
(27, 'COMP 003', 'Computer Programming 2', 3, 2, 3, 1, 0, NULL),
(28, 'COMP 004', 'Discrete Structures 1', 3, 3, 0, 1, 0, NULL),
(29, 'COMP 024', 'Technopreneurship', 3, 3, 0, 1, 0, NULL),
(30, 'CWTS 002', 'Civic Welfare Training Service 2', 3, 3, 0, 1, 0, NULL),
(31, 'GEED 001', 'Understanding the Self/Pag-unawa sa Sarili', 3, 3, 0, 1, 0, NULL),
(32, 'GEED 007', 'Science, Technology and Society/Agham, Teknolohiya, at Lipunan', 3, 3, 0, 1, 0, NULL),
(33, 'ITEC 103', 'Hardware/Software Installation and Maintenance', 3, 2, 3, 1, 0, NULL),
(34, 'ITEC 104', 'Basic Electronics', 3, 2, 3, 1, 0, NULL),
(35, 'PATHFIT 2', 'Physical Activity Towards Health and Fitness 2', 2, 2, 0, 1, 0, NULL),
(36, 'COMP 001', 'Introduction to Computing', 3, 2, 3, 1, 0, NULL),
(37, 'COMP 002', 'Computer Programming 1', 3, 2, 3, 1, 0, NULL),
(38, 'CWTS 001', 'Civic Welfare Training Service 1', 3, 3, 0, 1, 0, NULL),
(39, 'GEED 004', 'Mathematics in the Modern World/Matematika sa Makabagong Daigdig', 3, 3, 0, 1, 0, NULL),
(40, 'GEED 005', 'Purposive Communication/Malayuning Komunikasyon', 3, 3, 0, 1, 0, NULL),
(41, 'ITEC 101', 'Keyboarding and Documents Processing with Laboratory', 3, 2, 3, 1, 0, NULL),
(42, 'ITEC 102', 'Basic Computer Hardware Servicing', 3, 2, 3, 1, 0, NULL),
(43, 'PATHFIT 1', 'Physical Activity Towards Health and Fitness 1', 2, 2, 0, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblcourse_prerequisite`
--

CREATE TABLE `tblcourse_prerequisite` (
  `course_id` int(11) NOT NULL,
  `prereq_course_id` int(11) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcourse_prerequisite`
--

INSERT INTO `tblcourse_prerequisite` (`course_id`, `prereq_course_id`, `is_deleted`, `deleted_at`) VALUES
(1, 27, 0, NULL),
(5, 10, 0, NULL),
(8, 10, 0, NULL),
(10, 25, 0, NULL),
(12, 22, 0, NULL),
(14, 22, 0, NULL),
(19, 26, 0, NULL),
(21, 36, 0, NULL),
(24, 27, 0, NULL),
(26, 35, 0, NULL),
(28, 39, 0, NULL),
(33, 42, 0, NULL),
(35, 43, 0, NULL),
(37, 36, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbldepartment`
--

CREATE TABLE `tbldepartment` (
  `dept_id` int(11) NOT NULL,
  `dept_code` varchar(10) NOT NULL,
  `dept_name` varchar(100) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbldepartment`
--

INSERT INTO `tbldepartment` (`dept_id`, `dept_code`, `dept_name`, `is_deleted`, `deleted_at`) VALUES
(1, 'CIS', 'College of Computer and Information Sciences', 0, NULL),
(2, 'EDUC', 'College of Education', 0, NULL),
(3, 'ENG', 'College of Engineering', 0, NULL),
(4, 'SOC', 'College of Social Sciences', 0, NULL),
(5, 'OMT', 'College of Office Management and Technology', 0, NULL),
(6, 'BUS', 'College of Business Administration', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblenrollment`
--

CREATE TABLE `tblenrollment` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `date_enrolled` date NOT NULL,
  `status` enum('Active','Dropped','Completed') DEFAULT 'Active',
  `letter_grade` varchar(5) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblenrollment`
--

INSERT INTO `tblenrollment` (`enrollment_id`, `student_id`, `section_id`, `date_enrolled`, `status`, `letter_grade`, `is_deleted`, `deleted_at`) VALUES
(1, 27, 14, '2025-08-15', 'Active', 'B', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblinstructor`
--

CREATE TABLE `tblinstructor` (
  `instructor_id` int(11) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblinstructor`
--

INSERT INTO `tblinstructor` (`instructor_id`, `last_name`, `first_name`, `email`, `dept_id`, `is_deleted`, `deleted_at`) VALUES
(1, 'Santos', 'John Dustin', 'jddsantos@pup.edu.ph', 1, 0, NULL),
(2, 'Santos', 'Aren Dred', 'arendred.santos@pup.edu.ph', 1, 0, NULL),
(3, 'Minalabag', 'Christian Jim', 'jim.minalabag@pup.edu.ph', 3, 0, NULL),
(4, 'Modesto', 'Lady Melinda', 'ladymelindaminette.modesto@pup.edu.ph', 1, 0, NULL),
(5, 'Villarosa', 'Steven', 'ssvillarosa@gmail.com', 1, 0, NULL),
(6, 'Almirañez', 'Gecilie', 'almiranez.gecilie@gmail.com', 1, 0, NULL),
(7, 'San Luis', 'Angelo Joshua', 'sanluis.aj@pup.edu.ph', 1, 0, NULL),
(8, 'Franco', 'Francis', 'prof.francis.franco@gmail.com', 1, 0, NULL),
(9, 'Lingo', 'Orlando', 'lingo.orlando@pup.edu.ph', 1, 0, NULL),
(10, 'Tengco', 'Ronald Joy', 'tengco.ronaldjoy@pup.edu.ph', 1, 0, NULL),
(11, 'Canlas', 'Bernadette', 'canlas.bernadetter@gmail.com', 2, 0, NULL),
(12, 'Angeles', 'Nelson', 'angeles.nelson@pup.edu.ph', 1, 0, NULL),
(13, 'Villacorta', 'Karl Christian', 'villacorta.karlchristian@pup.edu.ph', 2, 0, NULL),
(14, 'Garcia', 'Mhel', 'garcia.mhel@pup.edu.ph', 5, 0, NULL),
(15, 'Bayot', 'Vicente', 'bayot.vicente@pup.edu.ph', 2, 0, NULL),
(16, 'Lim', 'Evangeline', 'lim.evangeline@pup.edu.ph', 2, 0, NULL),
(17, 'Ortega', 'Israel', 'ortega.israel@pup.edu.ph', 2, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblprogram`
--

CREATE TABLE `tblprogram` (
  `program_id` int(11) NOT NULL,
  `program_code` varchar(20) NOT NULL,
  `program_name` varchar(100) NOT NULL,
  `dept_id` int(11) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblprogram`
--

INSERT INTO `tblprogram` (`program_id`, `program_code`, `program_name`, `dept_id`, `is_deleted`, `deleted_at`) VALUES
(1, 'BSECE', 'Bachelor of Science in Electronics Engineering ', 3, 0, NULL),
(2, 'BSIT', 'Bachelor of Science in Information Technology', 1, 0, NULL),
(3, 'BSED_MATH', 'Bachelor in Secondary Education Major in Mathematics', 2, 0, NULL),
(4, 'BSME', 'Bachelor of Science in Mechanical Engineering', 3, 0, NULL),
(5, 'DIT', 'Diploma in Information Technology', 1, 0, NULL),
(6, 'BSED_ENGLISH', 'Bachelor in Secondary Education Major in English', 2, 0, NULL),
(7, 'BSPSY', 'Bachelor of Science in Psychology', 4, 0, NULL),
(8, 'BSBA', 'Bachelor of Science in Business Administration', 6, 0, NULL),
(9, 'BSBA-HRM', 'Bachelor of Science in Business Administration Major in Human Resource Management', 6, 0, NULL),
(10, 'BSOA', 'Bachelor of Science in Office Administration', 5, 0, NULL),
(11, 'DOMT', 'Diploma in Office Management Technology', 5, 0, NULL),
(12, 'BSBA-MM', 'Bachelor of Science in Business Administration major in Marketing Management', 6, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblroom`
--

CREATE TABLE `tblroom` (
  `room_id` int(11) NOT NULL,
  `building` varchar(50) NOT NULL,
  `room_code` varchar(20) NOT NULL,
  `capacity` int(11) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblroom`
--

INSERT INTO `tblroom` (`room_id`, `building`, `room_code`, `capacity`, `is_deleted`, `deleted_at`) VALUES
(1, 'Building A', 'DOST Laboratory', 60, 0, NULL),
(2, 'Building A', 'A-204', 60, 0, NULL),
(3, 'Building A', 'A-203', 60, 0, NULL),
(4, 'Building A', 'A-202', 60, 0, NULL),
(5, 'Building A', 'Aboitiz Laboratory', 40, 0, NULL),
(6, 'Building A', 'A-301', 60, 0, NULL),
(7, 'Building A', 'A-302', 60, 0, NULL),
(8, 'Building A', 'A-303', 60, 0, NULL),
(9, 'Building A', 'A-304', 60, 0, NULL),
(10, 'Building A', 'A-305', 60, 0, NULL),
(11, 'Building A', 'Bayers Lab', 40, 0, NULL),
(12, 'Building A', 'A-401', 60, 0, NULL),
(13, 'Building A', 'A-402', 60, 0, NULL),
(14, 'Building A', 'A-403', 60, 0, NULL),
(15, 'Building A', 'A-404', 60, 0, NULL),
(16, 'Building A', 'A-405', 60, 0, NULL),
(18, 'Building B', 'B-101', 40, 0, NULL),
(19, 'Engr Bldg', 'E-102', 40, 0, NULL),
(20, 'Engr Bldg', 'E-201', 40, 0, NULL),
(21, 'Engr Bldg', 'E-202', 40, 0, NULL),
(22, 'Engr Bldg', 'E-203', 40, 0, NULL),
(23, 'Engr Bldg', 'E-301', 40, 0, NULL),
(24, 'Engr Bldg', 'E-302', 40, 0, NULL),
(25, 'Engr Bldg', 'E-303', 40, 0, NULL),
(26, 'Engr Bldg', 'AVR', 50, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblsection`
--

CREATE TABLE `tblsection` (
  `section_id` int(11) NOT NULL,
  `section_code` varchar(20) NOT NULL,
  `course_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `day_pattern` varchar(20) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `room_id` int(11) NOT NULL,
  `max_capacity` int(11) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblsection`
--

INSERT INTO `tblsection` (`section_id`, `section_code`, `course_id`, `term_id`, `instructor_id`, `day_pattern`, `start_time`, `end_time`, `room_id`, `max_capacity`, `is_deleted`, `deleted_at`) VALUES
(1, 'DIT3-1', 1, 1, 7, 'S', '08:00:00', '12:30:00', 1, 32, 0, NULL),
(2, 'DIT3-1', 2, 1, 1, 'S', '18:30:00', '21:00:00', 5, 32, 0, NULL),
(3, 'DIT3-1', 3, 1, 2, 'Th', '16:00:00', '21:00:00', 5, 32, 0, NULL),
(4, 'DIT3-1', 4, 1, 3, 'F', '10:00:00', '14:00:00', 5, 32, 0, NULL),
(11, 'DIT3-1', 5, 1, 5, 'S', '13:00:00', '18:00:00', 11, 24, 0, NULL),
(12, 'DIT3-1', 6, 1, 6, 'T', '10:30:00', '15:30:00', 5, 32, 0, NULL),
(13, 'DIT3-1', 8, 1, 4, 'T', '17:00:00', '20:00:00', 5, 32, 0, NULL),
(14, 'DIT3-1', 9, 1, 4, 'T', '17:00:00', '20:00:00', 5, 32, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblstudent`
--

CREATE TABLE `tblstudent` (
  `student_id` int(11) NOT NULL,
  `student_no` varchar(20) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_initial` varchar(5) DEFAULT NULL COMMENT 'Student middle initial',
  `email` varchar(100) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `birthdate` date NOT NULL,
  `year_level` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstudent`
--

INSERT INTO `tblstudent` (`student_id`, `student_no`, `last_name`, `first_name`, `middle_initial`, `email`, `gender`, `birthdate`, `year_level`, `program_id`, `is_deleted`, `deleted_at`) VALUES
(1, '2023-00424-TG-0', 'Alejandro', 'Aleck Mcklaiyre', NULL, 'aleck.alejandro04@gmail.com', 'Female', '2004-09-03', 3, 5, 0, NULL),
(2, '2023-00425-TG-0', 'Andaya', 'Gener Jr.', NULL, 'generandaya4@gmail.com', 'Male', '2004-09-17', 3, 5, 0, NULL),
(3, '2023-00426-TG-0', 'Arroyo', 'John Matthew', NULL, 'johnmatthewarroyo2@gmail.com', 'Male', '2005-01-01', 3, 5, 0, NULL),
(4, '2023-00427-TG-0', 'Barcelos', 'Kevin Joseph', NULL, 'barceloskevinjoseph@gmail.com', 'Male', '2005-02-18', 3, 5, 0, NULL),
(5, '2023-00429-TG-0', 'Citron', 'Kathleen', NULL, 'citron.kathleen@gmail.com', 'Female', '2005-09-20', 3, 5, 0, NULL),
(6, '2023-00431-TG-0', 'Consultado', 'Kirby', NULL, 'kirbyconsultado@gmail.com', 'Male', '2005-09-10', 3, 5, 0, NULL),
(7, '2023-00432-TG-0', 'De Leon', 'Jasmine Robelle', NULL, 'yaskyeria@gmail.com', 'Female', '2004-08-04', 3, 5, 0, NULL),
(8, '2023-00433-TG-0', 'Delima', 'Justine', NULL, 'delimajustine24@gmail.com', 'Male', '2005-02-24', 3, 5, 0, NULL),
(9, '2023-00496-TG-0', 'Delumen', 'Ivan', NULL, 'ivandelumen05@gmail.com', 'Male', '2004-10-28', 3, 5, 0, NULL),
(10, '2023-00434-TG-0', 'Durante', 'Stephanie', NULL, 'durantestephanie07@gmail.com', 'Female', '2005-02-22', 3, 5, 0, NULL),
(11, '2023-00435-TG-0', 'Esparagoza', 'Mikka Kette', NULL, 'esparagozamikkakette@gmail.com', 'Female', '2004-11-12', 3, 5, 0, NULL),
(12, '2023-00436-TG-0', 'Florido', 'Maydelyn', NULL, 'maydelynflorido07@gmail.com', 'Female', '2005-06-07', 3, 5, 0, NULL),
(13, '2023-00437-TG-0', 'Francisco', 'Krislyn Janelle', NULL, 'krislynfrancisco0815@gmail.com', 'Female', '2005-08-20', 3, 5, 0, NULL),
(14, '2023-00438-TG-0', 'Genandoy', 'Hannah Lorainne', NULL, 'hann000345@gmail.com', 'Female', '2005-04-03', 3, 5, 0, NULL),
(15, '2023-00439-TG-0', 'Gomez', 'Ashley Hermione', NULL, 'hermionegomez49@gmail.com', 'Female', '2005-07-10', 3, 5, 0, NULL),
(16, '2023-00498-TG-0', 'Lazaro', 'Franco Alfonso', NULL, 'francoalfonso411@gmail.com', 'Male', '2005-04-11', 3, 5, 0, NULL),
(17, '2023-00440-TG-0', 'Mamasalanang', 'Gerald', NULL, 'cscb.vpr.gerald@gmail.com', 'Male', '2005-10-26', 3, 5, 0, NULL),
(18, '2023-00441-TG-0', 'Mejares', 'James Michael', NULL, 'jamesmichaelmejares@gmail.com', 'Male', '2005-11-07', 3, 5, 0, NULL),
(19, '2023-00443-TG-0', 'Mosquito', 'Michael Angelo', NULL, 'michaelmosquito147@gmail.com', 'Male', '2005-06-08', 3, 5, 0, NULL),
(20, '2023-00519-TG-0', 'Nolluda', 'John Carlo', NULL, 'johncarlonolluda@gmail.com', 'Male', '2004-09-18', 3, 5, 0, NULL),
(21, '2023-00444-TG-0', 'Piadozo', 'Edriane', NULL, 'piadozoedriane@gmail.com', 'Male', '2005-01-01', 3, 5, 0, NULL),
(22, '2023-00445-TG-0', 'Quiambao', 'Ma. Patricia Anne', NULL, 'patriciaquiambao078@gmail.com', 'Female', '2004-07-13', 3, 5, 0, NULL),
(23, '2023-00446-TG-0', 'Relente', 'Patricia Joy', NULL, 'relente.patriciajoy@gmail.com', 'Female', '2004-01-03', 3, 5, 0, NULL),
(24, '2023-00447-TG-0', 'Reyes', 'Simone Jake', NULL, 'reyesjake262@gmail.com', 'Male', '2004-11-17', 3, 5, 0, NULL),
(25, '2023-00448-TG-0', 'Riomalos', 'Zyrrah Feil', NULL, 'zriomalos@gmail.com', 'Female', '2005-09-24', 3, 5, 0, NULL),
(26, '2023-00449-TG-0', 'Siervo', 'Jallaine Perpetua', NULL, 'jallainesiervo143@gmail.com', 'Female', '2004-08-23', 3, 5, 0, NULL),
(27, '2023-00450-TG-0', 'Uy', 'Angelica Joy', NULL, 'angelicajoyuy16@gmail.com', 'Female', '2004-12-16', 3, 5, 0, NULL),
(28, '2023-00451-TG-0', 'Vesliño', 'Marc', NULL, 'marcveslino000@gmail.com', 'Male', '2005-05-04', 3, 5, 0, NULL),
(29, '2023-00495-TG-0', 'Victorioso', 'Daniel', NULL, 'danielvictorioso03@gmail.com', 'Male', '2000-11-05', 3, 5, 0, NULL),
(30, '2023-00452-TG-0', 'Villas', 'Clarence', NULL, 'villasclarence56@gmail.com', 'Female', '2004-10-04', 3, 5, 0, NULL),
(31, '2023-00453-TG-0', 'Ynion', 'Ma. Bea Mae', NULL, 'mabeamaeynion@gmail.com', 'Female', '2004-07-29', 3, 5, 0, NULL),
(32, '2024-00309-TG-0', 'Acido', 'Roland Renz', NULL, 'acidorenz22@gmail.com', 'Male', '2004-09-22', 2, 5, 0, NULL),
(33, '2024-00310-TG-0', 'Allego', 'Yuan Paolo', NULL, 'allegoyuanpaolo@gmail.com', 'Male', '2005-12-30', 2, 5, 0, NULL),
(34, '2024-00515-TG-0', 'Andador', 'Kim Phillip', NULL, 'andadorkimphillipg@gmail.com', 'Male', '2005-11-16', 2, 5, 0, NULL),
(35, '2024-00524-TG-0', 'Arellano', 'Charlz Kenneth', NULL, 'tkminer000@gmail.com', 'Male', '2003-12-09', 2, 5, 0, NULL),
(36, '2024-00311-TG-0', 'Ariba', 'Mariane Andrea', NULL, 'marianeariba12@gmail.com', 'Female', '2005-12-10', 2, 5, 0, NULL),
(37, '2024-00480-TG-0', 'Bangaysiso', 'Denze Gervin', NULL, 'denzegervin@gmail.com', 'Male', '2005-06-12', 2, 5, 0, NULL),
(38, '2024-00312-TG-0', 'Baquiran', 'Prinz Walter', NULL, 'baquiranprinzwalter@gmail.com', 'Male', '2006-10-09', 2, 5, 0, NULL),
(39, '2024-00313-TG-0', 'Bawlite', 'Aivan Gabriel', NULL, 'bawliteaivan@gmail.com', 'Male', '2006-08-08', 2, 5, 0, NULL),
(40, '2024-00314-TG-0', 'Bigtas', 'Jose Manuel', NULL, 'jmbigtasp0325@gmail.com', 'Male', '2006-03-25', 2, 5, 0, NULL),
(41, '2024-00315-TG-0', 'Cabasug', 'Francis Dale', NULL, 'franciscabasug26@gmail.com', 'Male', '0006-12-25', 2, 5, 0, NULL),
(42, '2024-00368-TG-0', 'Cabiades', 'Stephen Cedric', NULL, 'sccabiades@gmail.com', 'Male', '2006-02-09', 2, 5, 0, NULL),
(43, '2024-00317-TG-0', 'Castillo', 'John Paul', NULL, 'castillojohnpaul001@gmail.com', 'Male', '2005-02-19', 2, 5, 0, NULL),
(44, '2024-00320-TG-0', 'Castro', 'John Vincent', NULL, 'castrojohn105@gmail.com', 'Male', '2006-05-17', 2, 5, 0, NULL),
(45, '2024-00398-TG-0', 'Catalan', 'James Rolmer', NULL, 'jamescatalan19@gmail.com', 'Male', '2006-03-19', 2, 5, 0, NULL),
(46, '2024-00479-TG-0', 'Crisanto', 'Lyra', NULL, 'crisantolyra@gmail.com', 'Male', '2006-05-24', 2, 5, 0, NULL),
(47, '2024-00324-TG-0', 'Cruz', 'Arvin James', NULL, 'arvinjamescruz23@gmail.com', 'Male', '2006-08-23', 2, 5, 0, NULL),
(48, '2024-00325-TG-0', 'Dulaca', 'Amando III', NULL, 'amandodulacaiii0@gmail.com', 'Male', '2006-07-20', 2, 5, 0, NULL),
(49, '2024-00327-TG-0', 'Espedido', 'Narciso Miguel', NULL, 'migz9.narciso@gmail.com', 'Male', '2004-10-04', 2, 5, 0, NULL),
(50, '2024-00328-TG-0', 'Floresca', 'Duvan', NULL, 'duvanfloresca@gmail.com', 'Male', '2004-05-15', 2, 5, 0, NULL),
(51, '2024-00329-TG-0', 'Furaque', 'Patricia Hannah', NULL, 'furaquepatriciahannah@gmail.com', 'Female', '2004-12-08', 2, 5, 0, NULL),
(52, '2024-00330-TG-0', 'Libay', 'Jed', NULL, 'libayjeddelarema@gmail.comv', 'Male', '2006-02-07', 2, 5, 0, NULL),
(53, '2024-00332-TG-0', 'Limbaña', 'Renz Johanan', NULL, 'renzlimbana@gmail.com', 'Male', '2005-02-26', 2, 5, 0, NULL),
(54, '2024-00333-TG-0', 'Lipata', 'Hanz Gemuel', NULL, 'lipatagemuelhanzy@gmail.com', 'Male', '2006-05-09', 2, 5, 0, NULL),
(55, '2024-00334-TG-0', 'Lopez', 'Xander Ney', NULL, 'lopez.xander.ney016@gmail.com', 'Male', '2005-05-12', 2, 5, 0, NULL),
(56, '2024-00337-TG-0', 'Mabalo', 'Jeremiah', NULL, 'mabalojeremiah@gmail.com', 'Male', '2006-08-07', 2, 5, 0, NULL),
(57, '2024-00340-TG-0', 'Mandapat', 'Lloyd Frederick ', NULL, 'lloyd.mandapat36@gmail.com', 'Male', '2006-03-06', 2, 5, 0, NULL),
(58, '2024-00341-TG-0', 'Mariano', 'Iya Leonora', NULL, 'iyaonairam@gmail.com', 'Female', '2006-12-16', 2, 5, 0, NULL),
(59, '2024-00343-TG-0', 'Mejilla', 'Hezekiah', NULL, 'hrmejilla@gmail.com', 'Male', '2005-12-30', 2, 5, 0, NULL),
(60, '2024-00346-TG-0', 'Meneses', 'Daniel', NULL, 'danielmeneses434@gmail.com', 'Male', '2006-02-06', 2, 5, 0, NULL),
(61, '2024-00351-TG-0', 'Nale', 'Luther Ian', NULL, 'lutheriannale@gmail.com', 'Male', '2005-03-19', 2, 5, 0, NULL),
(62, '2024-00353-TG-0', 'Navarro', 'Leanne Jean', NULL, 'leannejn4@gmail.com', 'Female', '2006-07-04', 2, 5, 0, NULL),
(63, '2024-00369-TG-0', 'Pascua', 'Vlee Joel', NULL, 'vleepascua04@gmail.com', 'Male', '2006-04-04', 2, 5, 0, NULL),
(64, '2024-00356-TG-0', 'Ramos', 'John Renz', NULL, 'johnrenzr03@gmail.com', 'Male', '2003-08-23', 2, 5, 0, NULL),
(65, '2024-00357-TG-0', 'Reniva', 'Rolando Miguel', NULL, 'miggireniva123@gmail.com', 'Male', '2005-12-16', 2, 5, 0, NULL),
(66, '2024-00358-TG-0', 'Salosagcol', 'Marco Miguel', NULL, 'smarcomiguel222@gmail.com', 'Male', '2005-08-28', 2, 5, 0, NULL),
(67, '2024-00359-TG-0', 'Salvador', 'Mary Elizabeth', NULL, 'maryelizabeth09584@gmail.com', 'Female', '0000-00-00', 2, 5, 0, NULL),
(68, '2024-00360-TG-0', 'Samuya', 'Avelino Joseph', NULL, 'avelinosamuya1@gmail.com', 'Male', '2005-04-24', 2, 5, 0, NULL),
(69, '2024-00361-TG-0', 'Sanchez', 'Gabriel', NULL, 'gabriel.raknchez@gmail.com', 'Male', '2005-03-01', 2, 5, 0, NULL),
(70, '2024-00362-TG-0', 'Sequite', 'Kurt Laurence', NULL, 'kurtlaurencesequite23@gmail.com', 'Male', '2006-05-22', 2, 5, 0, NULL),
(71, '2024-00364-TG-0', 'Tilog', 'Zyron Drei', NULL, 'tilogzyrondrei@gmail.com', 'Male', '2005-11-30', 2, 5, 0, NULL),
(72, '2024-00365-TG-0', 'Tolentino', 'Vincent Johan', NULL, 'vincentjohantolentino@gmail.com', 'Male', '2005-05-06', 2, 5, 0, NULL),
(73, '2024-00366-TG-0', 'Valila', 'Lhuise Gahbrielle', NULL, 'gahbie.valila@gmail.com', 'Male', '2006-09-28', 2, 5, 0, NULL),
(74, '2024-00367-TG-0', 'Vasquez', 'Clark Justin', NULL, 'vasquezclarkjustin2006@gmail.com', 'Male', '2006-08-10', 2, 5, 0, NULL),
(75, '2025-00101-TG-0', 'Abanang', 'Ruzzel Andrei Velasquez', NULL, 'ruzzel.velasquez@pup.edu.ph', 'Male', '2005-04-18', 1, 5, 0, NULL),
(76, '2025-00102-TG-0', 'Adto', 'Daniel Perez', NULL, 'daniel.perez@pup.edu.ph', 'Male', '2004-09-21', 1, 5, 0, NULL),
(77, '2025-00103-TG-0', 'Aldeza', 'Gabriel Dathan Matienzo', NULL, 'gabriel.matienzo@pup.edu.ph', 'Male', '2005-06-05', 1, 5, 0, NULL),
(78, '2025-00104-TG-0', 'Angco', 'Micaella Lucas', NULL, 'micaella.lucas@pup.edu.ph', 'Female', '2005-02-12', 1, 5, 0, NULL),
(79, '2025-00105-TG-0', 'Arnocillo', 'Andre Santos', NULL, 'andre.santos@pup.edu.ph', 'Male', '2004-07-29', 1, 5, 0, NULL),
(80, '2025-00106-TG-0', 'Boghialbal', 'Devan Samudio', NULL, 'devan.samudio@pup.edu.ph', 'Male', '2005-03-25', 1, 5, 0, NULL),
(81, '2025-00107-TG-0', 'Botial', 'Christian Kim Tigtig', NULL, 'christian.tigtig@pup.edu.ph', 'Male', '2004-01-09', 1, 5, 0, NULL),
(82, '2025-00108-TG-0', 'Caceres', 'Mark Kenneth Calamlam', NULL, 'mark.calamlam@pup.edu.ph', 'Male', '2003-10-22', 1, 5, 0, NULL),
(83, '2025-00109-TG-0', 'Cho', 'Taisang Barances', NULL, 'taisang.barances@pup.edu.ph', 'Male', '2005-05-06', 1, 5, 0, NULL),
(84, '2025-00110-TG-0', 'Cudera', 'Lorenz Samuel Yoayao', NULL, 'lorenz.yoayao@pup.edu.ph', 'Male', '2004-11-30', 1, 5, 0, NULL),
(85, '2025-00111-TG-0', 'Daza', 'Dilan Higino', NULL, 'dilan.higino@pup.edu.ph', 'Male', '2005-08-14', 1, 5, 0, NULL),
(86, '2025-00112-TG-0', 'De Guzman', 'Steven Zantier Tamondong', NULL, 'steven.tamondong@pup.edu.ph', 'Male', '2004-03-11', 1, 5, 0, NULL),
(87, '2025-00113-TG-0', 'Delos Santos', 'Kimberly Anne Nialas', NULL, 'kimberly.nialas@pup.edu.ph', 'Female', '2005-12-02', 1, 5, 0, NULL),
(88, '2025-00114-TG-0', 'Divinagracia', 'Floan Rainbow Petel', NULL, 'floan.petel@pup.edu.ph', 'Female', '2004-04-17', 1, 5, 0, NULL),
(89, '2025-00115-TG-0', 'Efson', 'Jhon Marco Fernando', NULL, 'jhon.fernando@pup.edu.ph', 'Male', '2005-07-28', 1, 5, 0, NULL),
(90, '2025-00116-TG-0', 'Felipe', 'April Baria', NULL, 'april.baria@pup.edu.ph', 'Female', '2004-02-06', 1, 5, 0, NULL),
(91, '2025-00117-TG-0', 'Fuenties', 'Dana Carlos', NULL, 'dana.carlos@pup.edu.ph', 'Female', '2005-09-19', 1, 5, 0, NULL),
(92, '2025-00118-TG-0', 'Gaococos', 'Angel Ces Tejada', NULL, 'angel.tejada@pup.edu.ph', 'Female', '2004-10-13', 1, 5, 0, NULL),
(93, '2025-00119-TG-0', 'Gatchalian', 'Edward Daeo Dialo', NULL, 'edward.dialo@pup.edu.ph', 'Male', '2003-06-07', 1, 5, 0, NULL),
(94, '2025-00120-TG-0', 'Glifonea', 'Alexander King', NULL, 'alexander.king@pup.edu.ph', 'Male', '2004-05-25', 1, 5, 0, NULL),
(95, '2025-00121-TG-0', 'Gutierrez', 'Ghail Nashane Sumagsay', NULL, 'ghail.sumagsay@pup.edu.ph', 'Female', '2005-03-03', 1, 5, 0, NULL),
(96, '2025-00122-TG-0', 'Hicero', 'Austin Montañano', NULL, 'austin.montanano@pup.edu.ph', 'Male', '2004-07-10', 1, 5, 0, NULL),
(97, '2025-00123-TG-0', 'Huertas', 'Erica Fajardo', NULL, 'erica.fajardo@pup.edu.ph', 'Female', '2005-01-15', 1, 5, 0, NULL),
(98, '2025-00124-TG-0', 'Lorenzo', 'Caleb Miguel Escano', NULL, 'caleb.escano@pup.edu.ph', 'Male', '2003-09-26', 1, 5, 0, NULL),
(99, '2025-00125-TG-0', 'Magbanua', 'Julian Therese Abregana', NULL, 'julian.abregana@pup.edu.ph', 'Female', '2004-08-20', 1, 5, 0, NULL),
(100, '2025-00126-TG-0', 'Mansibang', 'Friyah Caszandra Bullecer', NULL, 'friyah.bullecer@pup.edu.ph', 'Female', '2005-11-11', 1, 5, 0, NULL),
(101, '2025-00127-TG-0', 'Mansugong', 'Eegan Carl Abner', NULL, 'eegan.abner@pup.edu.ph', 'Male', '2004-12-18', 1, 5, 0, NULL),
(102, '2025-00128-TG-0', 'Murillo', 'Jus Andyn Bonagantay', NULL, 'jus.bonagantay@pup.edu.ph', 'Male', '2003-10-30', 1, 5, 0, NULL),
(103, '2025-00129-TG-0', 'Naron', 'Jhonas Jay', NULL, 'jhonas.jay@pup.edu.ph', 'Male', '2004-06-12', 1, 5, 0, NULL),
(104, '2025-00130-TG-0', 'Paccial', 'Jerichos Celdas', NULL, 'jerichos.celdas@pup.edu.ph', 'Male', '2005-04-09', 1, 5, 0, NULL),
(105, '2025-00131-TG-0', 'Pacer', 'Kim Justin Cortes', NULL, 'kim.cortes@pup.edu.ph', 'Male', '2004-02-22', 1, 5, 0, NULL),
(106, '2025-00132-TG-0', 'Palite', 'Ephraim Villanueva', NULL, 'ephraim.villanueva@pup.edu.ph', 'Male', '2005-08-05', 1, 5, 0, NULL),
(107, '2025-00133-TG-0', 'Pastrana', 'Noel Celerbro', NULL, 'noel.celerbro@pup.edu.ph', 'Male', '2003-11-14', 1, 5, 0, NULL),
(108, '2025-00134-TG-0', 'Penid', 'Joshua Moril', NULL, 'joshua.moril@pup.edu.ph', 'Male', '2004-09-03', 1, 5, 0, NULL),
(109, '2025-00135-TG-0', 'Pepito', 'Michael Rhey Arce', NULL, 'michael.arce@pup.edu.ph', 'Male', '2005-05-30', 1, 5, 0, NULL),
(110, '2025-00136-TG-0', 'Portas', 'Jewel Jomer Nash Laureta', NULL, 'jewel.laureta@pup.edu.ph', 'Male', '2004-03-28', 1, 5, 0, NULL),
(111, '2025-00137-TG-0', 'Rafael', 'Aaron Lemuel Requion', NULL, 'aaron.requion@pup.edu.ph', 'Male', '2005-07-07', 1, 5, 0, NULL),
(112, '2025-00138-TG-0', 'Ramilo', 'Meijon Florence Fernandez', NULL, 'meijon.fernandez@pup.edu.ph', 'Female', '2004-01-26', 1, 5, 0, NULL),
(113, '2025-00139-TG-0', 'Reli', 'Marco Dazo', NULL, 'marco.dazo@pup.edu.ph', 'Male', '2005-10-17', 1, 5, 0, NULL),
(114, '2025-00140-TG-0', 'Resma', 'Jhon Philip Laureano', NULL, 'jhon.laureano@pup.edu.ph', 'Male', '2004-12-01', 1, 5, 0, NULL),
(115, '2025-00141-TG-0', 'Rosales', 'Jermaine De Baraacor', NULL, 'jermaine.baraacor@pup.edu.ph', 'Male', '2005-02-27', 1, 5, 0, NULL),
(116, '2025-00142-TG-0', 'Siladan', 'Jeremiah Armas', NULL, 'jeremiah.armas@pup.edu.ph', 'Male', '2004-04-04', 1, 5, 0, NULL),
(117, '2025-00143-TG-0', 'Tapic', 'Neo Praecellus', NULL, 'neotapic21@gmail.com', 'Male', '2005-12-03', 1, 5, 0, NULL),
(118, '2025-00465- TG-0	', 'Traqueña', 'Lyka Ericka', NULL, 'traquena.lykaerica@pup.edu.ph', 'Female', '2005-08-08', 1, 5, 0, NULL),
(119, '2025-00466-TG-0	', 'Varron', 'Avner Roi', NULL, 'avnerroivarron11@gamil.com', 'Male', '2007-10-11', 1, 5, 0, NULL),
(120, '2025-00467-TG-0', 'Villagarcia	', 'Dion Alexander', NULL, 'dionalexandervillagarcia@gmail.com', 'Male', '2006-03-03', 1, 5, 0, NULL),
(121, '2025-00468-TG-0', 'Yulo', 'Thyonne Pierre', NULL, 'yulo.thyonnepierre@pup.edu.ph', 'Male', '2006-09-09', 1, 5, 0, NULL),
(122, '2025-00469-TG-0	', 'Zagada', 'John Joshua', NULL, 'zagada.johnjoshua@pup.edu.ph', 'Male', '2005-10-10', 1, 5, 0, NULL),
(123, '2025-00503-TG-0	', 'Salazar', 'Junior Cesar', NULL, 'salazarjc030@gmail.com', 'Male', '2007-05-25', 1, 5, 0, NULL),
(124, '2025-00425-TG-0', 'Bacsal', 'Justin', NULL, 'justinbacsal35@gmail.com', 'Male', '2007-12-24', 1, 5, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblterm`
--

CREATE TABLE `tblterm` (
  `term_id` int(11) NOT NULL,
  `term_code` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblterm`
--

INSERT INTO `tblterm` (`term_id`, `term_code`, `start_date`, `end_date`, `is_deleted`, `deleted_at`) VALUES
(1, 'First Semester 2025', '2025-09-01', '2026-01-17', 0, NULL),
(2, 'Second Semester 2025', '2026-02-09', '2026-06-21', 0, NULL),
(3, 'Summer Term', '2026-06-29', '2026-08-08', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblcourse`
--
ALTER TABLE `tblcourse`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `course_code` (`course_code`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `tblcourse_prerequisite`
--
ALTER TABLE `tblcourse_prerequisite`
  ADD PRIMARY KEY (`course_id`,`prereq_course_id`),
  ADD KEY `prereq_course_id` (`prereq_course_id`);

--
-- Indexes for table `tbldepartment`
--
ALTER TABLE `tbldepartment`
  ADD PRIMARY KEY (`dept_id`),
  ADD UNIQUE KEY `dept_code` (`dept_code`);

--
-- Indexes for table `tblenrollment`
--
ALTER TABLE `tblenrollment`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `tblinstructor`
--
ALTER TABLE `tblinstructor`
  ADD PRIMARY KEY (`instructor_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `tblprogram`
--
ALTER TABLE `tblprogram`
  ADD PRIMARY KEY (`program_id`),
  ADD UNIQUE KEY `program_code` (`program_code`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `tblroom`
--
ALTER TABLE `tblroom`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_code` (`room_code`);

--
-- Indexes for table `tblsection`
--
ALTER TABLE `tblsection`
  ADD PRIMARY KEY (`section_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `term_id` (`term_id`),
  ADD KEY `instructor_id` (`instructor_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `tblstudent`
--
ALTER TABLE `tblstudent`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_no` (`student_no`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `tblterm`
--
ALTER TABLE `tblterm`
  ADD PRIMARY KEY (`term_id`),
  ADD UNIQUE KEY `term_code` (`term_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblcourse`
--
ALTER TABLE `tblcourse`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `tbldepartment`
--
ALTER TABLE `tbldepartment`
  MODIFY `dept_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblenrollment`
--
ALTER TABLE `tblenrollment`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tblinstructor`
--
ALTER TABLE `tblinstructor`
  MODIFY `instructor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tblprogram`
--
ALTER TABLE `tblprogram`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tblroom`
--
ALTER TABLE `tblroom`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `tblsection`
--
ALTER TABLE `tblsection`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tblstudent`
--
ALTER TABLE `tblstudent`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `tblterm`
--
ALTER TABLE `tblterm`
  MODIFY `term_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblcourse`
--
ALTER TABLE `tblcourse`
  ADD CONSTRAINT `tblcourse_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `tbldepartment` (`dept_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tblcourse_prerequisite`
--
ALTER TABLE `tblcourse_prerequisite`
  ADD CONSTRAINT `tblcourse_prerequisite_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `tblcourse` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tblcourse_prerequisite_ibfk_2` FOREIGN KEY (`prereq_course_id`) REFERENCES `tblcourse` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblenrollment`
--
ALTER TABLE `tblenrollment`
  ADD CONSTRAINT `tblenrollment_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `tblstudent` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tblenrollment_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `tblsection` (`section_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tblinstructor`
--
ALTER TABLE `tblinstructor`
  ADD CONSTRAINT `tblinstructor_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `tbldepartment` (`dept_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tblprogram`
--
ALTER TABLE `tblprogram`
  ADD CONSTRAINT `tblprogram_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `tbldepartment` (`dept_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tblsection`
--
ALTER TABLE `tblsection`
  ADD CONSTRAINT `tblsection_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `tblcourse` (`course_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tblsection_ibfk_2` FOREIGN KEY (`term_id`) REFERENCES `tblterm` (`term_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tblsection_ibfk_3` FOREIGN KEY (`instructor_id`) REFERENCES `tblinstructor` (`instructor_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tblsection_ibfk_4` FOREIGN KEY (`room_id`) REFERENCES `tblroom` (`room_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tblstudent`
--
ALTER TABLE `tblstudent`
  ADD CONSTRAINT `tblstudent_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `tblprogram` (`program_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
