-- Add tenant_identity to contracts (optional)
ALTER TABLE contracts ADD COLUMN tenant_identity VARCHAR(40) NULL AFTER status;
