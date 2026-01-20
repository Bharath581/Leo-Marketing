USE leomarketing;

-- 1. Create Newsletter Table
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Add 'timeline' column to contact_messages (for Service Quote forms)
-- We check if it exists by trying to add it. If it fails (duplicate column), it's fine in this context.
-- But standard SQL doesn't have "IF NOT EXISTS" for columns easily.
-- We will just run the ALTER command. If it errors because it exists, that's okay.
ALTER TABLE contact_messages ADD COLUMN timeline VARCHAR(100) AFTER budget;

-- 3. Add 'department' column to contact_messages (for Team form)
ALTER TABLE contact_messages ADD COLUMN department VARCHAR(100) AFTER service;
