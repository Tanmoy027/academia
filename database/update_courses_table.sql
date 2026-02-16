-- Add duration and is_active columns to courses table
ALTER TABLE courses ADD COLUMN IF NOT EXISTS duration VARCHAR(255) NULL;
ALTER TABLE courses ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE;
