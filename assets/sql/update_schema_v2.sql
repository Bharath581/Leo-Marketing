-- Add is_read column to contact_messages table
ALTER TABLE contact_messages ADD COLUMN is_read TINYINT(1) DEFAULT 0;

-- Optional: Create index for faster filtering
CREATE INDEX idx_is_read ON contact_messages(is_read);
