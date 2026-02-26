-- USER PROFILE MEDIA BUCKET SETUP
-- Run inside the Supabase SQL editor

-- 1. Ensure the bucket exists (public so avatars can be read without a token)
INSERT INTO storage.buckets (id, name, public)
VALUES ('userprofile', 'userprofile', true)
ON CONFLICT (id) DO UPDATE SET public = EXCLUDED.public;

-- 2. RLS is enabled on storage.objects by default in Supabase.
--    Uncomment the next line ONLY if you are running as the storage schema owner/service role.
-- ALTER TABLE storage.objects ENABLE ROW LEVEL SECURITY;

-- 3. Drop old policies if you are re-running this script to avoid duplicates
DO $$
BEGIN
    IF EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'User Profile Public Read') THEN
        DROP POLICY "User Profile Public Read" ON storage.objects;
    END IF;
    IF EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'User Profile Owner Insert') THEN
        DROP POLICY "User Profile Owner Insert" ON storage.objects;
    END IF;
    IF EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'User Profile Owner Update') THEN
        DROP POLICY "User Profile Owner Update" ON storage.objects;
    END IF;
    IF EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'User Profile Owner Delete') THEN
        DROP POLICY "User Profile Owner Delete" ON storage.objects;
    END IF;
END $$;

-- 4. Read policy - anyone can view avatars stored in the bucket
CREATE POLICY "User Profile Public Read"
ON storage.objects FOR SELECT
USING (bucket_id = 'userprofile');

-- 5. Insert policy - authenticated users can upload into their own folder
CREATE POLICY "User Profile Owner Insert"
ON storage.objects FOR INSERT
TO authenticated
WITH CHECK (
    bucket_id = 'userprofile'
    AND auth.uid() = owner
);

-- 6. Update policy - allow the owner to replace their files
CREATE POLICY "User Profile Owner Update"
ON storage.objects FOR UPDATE
TO authenticated
USING (
    bucket_id = 'userprofile'
    AND auth.uid() = owner
)
WITH CHECK (
    bucket_id = 'userprofile'
    AND auth.uid() = owner
);

-- 7. Delete policy - allow the owner to remove their files
CREATE POLICY "User Profile Owner Delete"
ON storage.objects FOR DELETE
TO authenticated
USING (
    bucket_id = 'userprofile'
    AND auth.uid() = owner
);

-- 8. (Optional) add an index to speed up lookups by bucket/key
CREATE INDEX IF NOT EXISTS storage_objects_bucket_name_idx
    ON storage.objects (bucket_id, name);
