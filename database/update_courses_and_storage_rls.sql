-- Add image_url column to courses table
ALTER TABLE courses ADD COLUMN IF NOT EXISTS image_url VARCHAR(255) NULL;

-- Enable RLS for storage.objects if not already enabled (Supabase usually has this)
-- ALTER TABLE storage.objects ENABLE ROW LEVEL SECURITY;

-- 2. Create Policy strictly for the bucket 'courseprofile'
-- Allow public SELECT (read) access to files in the 'courseprofile' bucket
CREATE POLICY "Public Access"
ON storage.objects FOR SELECT
USING ( bucket_id = 'courseprofile' );

-- Allow authenticated users (like Admin via generic generic authenticated role or service role) to INSERT
CREATE POLICY "Authenticated Insert"
ON storage.objects FOR INSERT
TO authenticated
WITH CHECK ( bucket_id = 'courseprofile' );


-- Allow authenticated users to UPDATE their own files or all files if Admin
-- For simplicity, allowing authenticated users to update files in this bucket
CREATE POLICY "Authenticated Update"
ON storage.objects FOR UPDATE
TO authenticated
USING ( bucket_id = 'courseprofile' );

-- Allow authenticated users to DELETE
CREATE POLICY "Authenticated Delete"
ON storage.objects FOR DELETE
TO authenticated
USING ( bucket_id = 'courseprofile' );
