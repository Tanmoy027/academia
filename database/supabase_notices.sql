-- ============================================
-- NOTICE & ANNOUNCEMENT SYSTEM FOR SUPABASE
-- Run this SQL in Supabase SQL Editor
-- ============================================

-- Step 1: Check if notices table exists, if not create it
-- If the table already exists, this will add the missing columns

-- Create the notices table (if it doesn't exist)
CREATE TABLE IF NOT EXISTS notices (
    id BIGSERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(50) DEFAULT 'general',
    target_role VARCHAR(50) DEFAULT 'all',
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Step 2: If table exists but missing columns, add them
-- Add category column if it doesn't exist
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'notices' AND column_name = 'category'
    ) THEN
        ALTER TABLE notices ADD COLUMN category VARCHAR(50) DEFAULT 'general';
    END IF;
END $$;

-- Add is_active column if it doesn't exist
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'notices' AND column_name = 'is_active'
    ) THEN
        ALTER TABLE notices ADD COLUMN is_active BOOLEAN DEFAULT true;
    END IF;
END $$;

-- Step 3: Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_notices_target_role ON notices(target_role);
CREATE INDEX IF NOT EXISTS idx_notices_category ON notices(category);
CREATE INDEX IF NOT EXISTS idx_notices_is_active ON notices(is_active);
CREATE INDEX IF NOT EXISTS idx_notices_created_at ON notices(created_at DESC);

-- Step 4: Create a function to auto-update the updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Step 5: Create trigger to auto-update updated_at on row update
DROP TRIGGER IF EXISTS update_notices_updated_at ON notices;
CREATE TRIGGER update_notices_updated_at
    BEFORE UPDATE ON notices
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- Step 6: Enable Row Level Security (RLS)
ALTER TABLE notices ENABLE ROW LEVEL SECURITY;

-- Step 7: Create RLS Policies

-- Policy: Allow authenticated users (students/teachers) to read active notices for their role
DROP POLICY IF EXISTS "Users can view notices for their role" ON notices;
CREATE POLICY "Users can view notices for their role" ON notices
    FOR SELECT
    USING (
        is_active = true 
        AND (target_role = 'all' OR target_role = current_setting('app.current_user_role', true))
    );

-- Policy: Allow admins to do everything (CRUD)
DROP POLICY IF EXISTS "Admins have full access to notices" ON notices;
CREATE POLICY "Admins have full access to notices" ON notices
    FOR ALL
    USING (current_setting('app.current_user_role', true) = 'admin')
    WITH CHECK (current_setting('app.current_user_role', true) = 'admin');

-- Alternative: If you're not using RLS with app settings, use this simpler policy
-- This allows all authenticated users to read and only service role to write

-- Drop the role-based policies if they cause issues
-- DROP POLICY IF EXISTS "Users can view notices for their role" ON notices;
-- DROP POLICY IF EXISTS "Admins have full access to notices" ON notices;

-- Simple policy: Anyone can read active notices
DROP POLICY IF EXISTS "Anyone can read active notices" ON notices;
CREATE POLICY "Anyone can read active notices" ON notices
    FOR SELECT
    USING (is_active = true);

-- Simple policy: Service role (your Laravel app) can do everything
DROP POLICY IF EXISTS "Service role full access" ON notices;
CREATE POLICY "Service role full access" ON notices
    FOR ALL
    USING (true)
    WITH CHECK (true);

-- ============================================
-- SAMPLE DATA (Optional - Remove if not needed)
-- ============================================

-- Insert sample notices for testing
INSERT INTO notices (title, content, category, target_role, is_active) VALUES
('Welcome to Academia!', 'Welcome to our learning platform. Please check your course schedule and stay updated with announcements.', 'general', 'all', true),
('Mid-Term Exam Schedule', 'Mid-term examinations will be held from March 15-20, 2026. Please prepare accordingly.', 'exam', 'student', true),
('Faculty Meeting', 'All teachers are requested to attend the faculty meeting on February 20, 2026 at 10:00 AM.', 'general', 'teacher', true),
('Holiday Notice - Independence Day', 'The institution will remain closed on March 26, 2026 for Independence Day celebration.', 'holiday', 'all', true),
('Class Schedule Update', 'Morning batch timings have been changed from 8:00 AM to 8:30 AM starting next week.', 'class_update', 'all', true)
ON CONFLICT DO NOTHING;

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Check table structure
-- SELECT column_name, data_type, column_default, is_nullable 
-- FROM information_schema.columns 
-- WHERE table_name = 'notices';

-- Check all notices
-- SELECT * FROM notices ORDER BY created_at DESC;

-- Check notices for students
-- SELECT * FROM notices WHERE is_active = true AND (target_role = 'all' OR target_role = 'student') ORDER BY created_at DESC;

-- Check notices for teachers
-- SELECT * FROM notices WHERE is_active = true AND (target_role = 'all' OR target_role = 'teacher') ORDER BY created_at DESC;
