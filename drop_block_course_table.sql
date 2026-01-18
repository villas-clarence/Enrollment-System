-- Drop tblblock_course table
-- This table is not being used in the current system
-- Safe to delete since it has no data and no foreign key references

DROP TABLE IF EXISTS `tblblock_course`;

-- Verification query (run this after dropping to confirm)
-- SHOW TABLES LIKE 'tblblock_course';
