-- COURSE MATERIAL STORAGE TABLE FOR SUPABASE
-- Run this script inside the Supabase SQL Editor if you prefer manual schema management.
-- Existing storage bucket RLS rules remain untouched.

create table if not exists public.course_materials (
    id bigserial primary key,
    course_id bigint not null references public.courses(id) on delete cascade,
    uploaded_by bigint not null references public.users(id) on delete cascade,
    title varchar(255) not null,
    description text null,
    file_path text not null,
    file_url text not null,
    file_type varchar(120) null,
    file_size bigint null,
    created_at timestamptz not null default timezone('utc', now()),
    updated_at timestamptz not null default timezone('utc', now())
);

create index if not exists course_materials_course_id_index on public.course_materials(course_id);
create index if not exists course_materials_uploaded_by_index on public.course_materials(uploaded_by);
