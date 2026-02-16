-- ============================================
-- STRIPE PAYMENTS SCHEMA UPDATE FOR SUPABASE
-- Run this SQL in Supabase SQL Editor
-- ============================================

-- Add new columns to payments table for Stripe integration
DO $$
BEGIN
    -- Add stripe_session_id column if it doesn't exist
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'payments' AND column_name = 'stripe_session_id'
    ) THEN
        ALTER TABLE payments ADD COLUMN stripe_session_id VARCHAR(255) NULL;
    END IF;
    
    -- Add payment_method column if it doesn't exist
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'payments' AND column_name = 'payment_method'
    ) THEN
        ALTER TABLE payments ADD COLUMN payment_method VARCHAR(50) DEFAULT 'stripe';
    END IF;
END $$;

-- Create index on stripe_session_id for faster lookups
CREATE INDEX IF NOT EXISTS idx_payments_stripe_session_id ON payments(stripe_session_id);

-- Create index on transaction_id for faster lookups
CREATE INDEX IF NOT EXISTS idx_payments_transaction_id ON payments(transaction_id);

-- Create index on status for filtering
CREATE INDEX IF NOT EXISTS idx_payments_status ON payments(status);

-- Update RLS policies if needed (optional - adjust based on your needs)
-- Enable RLS on payments table
ALTER TABLE payments ENABLE ROW LEVEL SECURITY;

-- Policy for users to view their own payments
DROP POLICY IF EXISTS "Users can view own payments" ON payments;
CREATE POLICY "Users can view own payments" ON payments
    FOR SELECT
    USING (auth.uid()::text = user_id::text OR auth.role() = 'service_role');

-- Policy for inserting payments (service role only for security)
DROP POLICY IF EXISTS "Service role can insert payments" ON payments;
CREATE POLICY "Service role can insert payments" ON payments
    FOR INSERT
    WITH CHECK (true);

-- Policy for updating payments
DROP POLICY IF EXISTS "Service role can update payments" ON payments;
CREATE POLICY "Service role can update payments" ON payments
    FOR UPDATE
    USING (true);

SELECT 'Payments table updated successfully for Stripe integration!' as result;
