-- Add Teacher Profile Fields to Users Table
-- This migration adds additional fields for teacher profiles

-- Add teacher-specific profile fields
ALTER TABLE users ADD COLUMN IF NOT EXISTS bio TEXT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(20) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS qualification TEXT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS specialization VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS experience_years INTEGER DEFAULT 0;
ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(500) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS social_links JSONB NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS address TEXT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS city VARCHAR(100) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS country VARCHAR(100) NULL;

-- Add comments for documentation
COMMENT ON COLUMN users.bio IS 'Teacher biography/about section';
COMMENT ON COLUMN users.phone IS 'Contact phone number';
COMMENT ON COLUMN users.qualification IS 'Educational qualifications and degrees';
COMMENT ON COLUMN users.specialization IS 'Subject specializations';
COMMENT ON COLUMN users.experience_years IS 'Years of teaching experience';
COMMENT ON COLUMN users.profile_picture IS 'URL to profile picture';
COMMENT ON COLUMN users.social_links IS 'JSON object containing social media links (linkedin, twitter, facebook, etc.)';
COMMENT ON COLUMN users.address IS 'Street address';
COMMENT ON COLUMN users.city IS 'City';
COMMENT ON COLUMN users.country IS 'Country';
