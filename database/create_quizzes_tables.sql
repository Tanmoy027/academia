-- Create quizzes table
CREATE TABLE quizzes (
    id BIGSERIAL PRIMARY KEY,
    course_id BIGINT NOT NULL,
    teacher_id BIGINT NOT NULL,
    title VARCHAR(500) NOT NULL,
    description TEXT,
    total_marks INTEGER NOT NULL,
    duration_minutes INTEGER NOT NULL,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    show_correct_answers BOOLEAN DEFAULT false,
    shuffle_questions BOOLEAN DEFAULT false,
    is_published BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_quizzes_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    CONSTRAINT fk_quizzes_teacher FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add comments for documentation
COMMENT ON COLUMN quizzes.duration_minutes IS 'Duration in minutes';

-- Create indexes for quizzes
CREATE INDEX idx_quizzes_course ON quizzes(course_id);
CREATE INDEX idx_quizzes_teacher ON quizzes(teacher_id);
CREATE INDEX idx_quizzes_published ON quizzes(is_published);
CREATE INDEX idx_quizzes_start_end ON quizzes(start_time, end_time);

-- Create questions table
CREATE TABLE questions (
    id BIGSERIAL PRIMARY KEY,
    quiz_id BIGINT NOT NULL,
    question_text TEXT NOT NULL,
    question_type VARCHAR(50) NOT NULL,
    marks INTEGER NOT NULL,
    "order" INTEGER NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_questions_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    CHECK (question_type IN ('mcq', 'short_answer')),
    CHECK (marks > 0)
);

-- Add comments for documentation
COMMENT ON COLUMN questions.question_type IS 'mcq or short_answer';

-- Create indexes for questions
CREATE INDEX idx_questions_quiz ON questions(quiz_id);
CREATE INDEX idx_questions_type ON questions(question_type);

-- Create question_options table (for MCQ options)
CREATE TABLE question_options (
    id BIGSERIAL PRIMARY KEY,
    question_id BIGINT NOT NULL,
    option_text TEXT NOT NULL,
    is_correct BOOLEAN DEFAULT false,
    
    CONSTRAINT fk_question_options_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- Create indexes for question_options
CREATE INDEX idx_question_options_question ON question_options(question_id);
CREATE INDEX idx_question_options_correct ON question_options(is_correct);

-- Create student_quiz_attempts table
CREATE TABLE student_quiz_attempts (
    id BIGSERIAL PRIMARY KEY,
    quiz_id BIGINT NOT NULL,
    student_id BIGINT NOT NULL,
    started_at TIMESTAMP,
    submitted_at TIMESTAMP,
    marks_obtained DECIMAL(8, 2) DEFAULT 0,
    percentage DECIMAL(5, 2) DEFAULT 0,
    is_submitted BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_student_quiz_attempts_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    CONSTRAINT fk_student_quiz_attempts_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT uq_student_quiz_attempts UNIQUE (quiz_id, student_id)
);

-- Create indexes for student_quiz_attempts
CREATE INDEX idx_student_quiz_attempts_quiz ON student_quiz_attempts(quiz_id);
CREATE INDEX idx_student_quiz_attempts_student ON student_quiz_attempts(student_id);
CREATE INDEX idx_student_quiz_attempts_submitted ON student_quiz_attempts(is_submitted);

-- Create student_responses table
CREATE TABLE student_responses (
    id BIGSERIAL PRIMARY KEY,
    student_quiz_attempt_id BIGINT NOT NULL,
    question_id BIGINT NOT NULL,
    selected_option_id BIGINT,
    answer_text TEXT,
    marks_awarded DECIMAL(8, 2),
    teacher_feedback TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_student_responses_attempt FOREIGN KEY (student_quiz_attempt_id) REFERENCES student_quiz_attempts(id) ON DELETE CASCADE,
    CONSTRAINT fk_student_responses_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    CONSTRAINT fk_student_responses_option FOREIGN KEY (selected_option_id) REFERENCES question_options(id) ON DELETE SET NULL
);

-- Create indexes for student_responses
CREATE INDEX idx_student_responses_attempt ON student_responses(student_quiz_attempt_id);
CREATE INDEX idx_student_responses_question ON student_responses(question_id);
CREATE INDEX idx_student_responses_graded ON student_responses(marks_awarded);
