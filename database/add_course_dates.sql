-- Add start_date and end_date columns to courses table
-- start_date: When the course starts (students cannot enroll after this date)
-- end_date: When the course ends (used for progress calculation)

ALTER TABLE courses ADD COLUMN IF NOT EXISTS start_date DATE NULL;
ALTER TABLE courses ADD COLUMN IF NOT EXISTS end_date DATE NULL;

-- Add class_type column for online delivery mode (offline removed)
-- Value: 'online'
ALTER TABLE courses ADD COLUMN IF NOT EXISTS class_type VARCHAR(20) DEFAULT 'online';

-- Add online_schedule column for virtual class timing details
ALTER TABLE courses ADD COLUMN IF NOT EXISTS online_schedule TEXT NULL;
ALTER TABLE courses ADD COLUMN IF NOT EXISTS offline_schedule TEXT NULL; -- legacy, unused

-- Add comment for clarity
COMMENT ON COLUMN courses.start_date IS 'Course start date - enrollment closes after this date';
COMMENT ON COLUMN courses.end_date IS 'Course end date - used for progress calculation';
COMMENT ON COLUMN courses.class_type IS 'Type of class: online only (offline mode removed)';
COMMENT ON COLUMN courses.online_schedule IS 'Schedule for online classes';
COMMENT ON COLUMN courses.offline_schedule IS 'Legacy column for removed offline schedule';
