-- Add start_date and end_date columns to courses table
-- start_date: When the course starts (students cannot enroll after this date)
-- end_date: When the course ends (used for progress calculation)

ALTER TABLE courses ADD COLUMN IF NOT EXISTS start_date DATE NULL;
ALTER TABLE courses ADD COLUMN IF NOT EXISTS end_date DATE NULL;

-- Add class_type column for online/offline/hybrid courses
-- Values: 'online', 'offline', 'hybrid'
ALTER TABLE courses ADD COLUMN IF NOT EXISTS class_type VARCHAR(20) DEFAULT 'offline';

-- Add online_schedule and offline_schedule for hybrid courses
-- These store which days/times are online vs offline
ALTER TABLE courses ADD COLUMN IF NOT EXISTS online_schedule TEXT NULL;
ALTER TABLE courses ADD COLUMN IF NOT EXISTS offline_schedule TEXT NULL;

-- Add comment for clarity
COMMENT ON COLUMN courses.start_date IS 'Course start date - enrollment closes after this date';
COMMENT ON COLUMN courses.end_date IS 'Course end date - used for progress calculation';
COMMENT ON COLUMN courses.class_type IS 'Type of class: online, offline, or hybrid (both)';
COMMENT ON COLUMN courses.online_schedule IS 'Schedule for online classes (for hybrid courses)';
COMMENT ON COLUMN courses.offline_schedule IS 'Schedule for offline classes (for hybrid courses)';
