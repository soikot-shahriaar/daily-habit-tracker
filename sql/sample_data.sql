-- Sample Data for Daily Habit Tracker CMS
-- This file contains sample data for testing and demonstration purposes

-- Update existing demo_user password if user already exists (for existing databases)
-- This ensures the password is always up-to-date whether running fresh or updating
UPDATE users 
SET password_hash = '$2y$10$lmcuRvctCfgk.4Elzsn79.RuVdV.6OoI/ZQKNxu42K7vv/Oncb3z2' 
WHERE username = 'demo_user';

-- Insert sample users (INSERT IGNORE will skip if users already exist)
INSERT IGNORE INTO users (username, email, password_hash, first_name, last_name) VALUES
('demo_user', 'demo@example.com', '$2y$10$lmcuRvctCfgk.4Elzsn79.RuVdV.6OoI/ZQKNxu42K7vv/Oncb3z2', 'Demo', 'User'),
('john_doe', 'john@example.com', '$2y$10$iDd0A10J9qqltblLZMPQ1uiMQ7BzLkpPQJn/BHWCTivY1UZh1Zy62', 'John', 'Doe'),
('jane_smith', 'jane@example.com', '$2y$10$0L7oCiLpbIBgbMoA48vYd.U8b2cL0fsFS6NPn08LfnS8.4jkAbPei', 'Jane', 'Smith');

-- Note: Demo user passwords (hashed with bcrypt):
-- demo_user: Demo123!
-- john_doe: john123  
-- jane_smith: jane123

-- Insert sample habits for demo_user (user_id = 1)
INSERT INTO habits (user_id, title, description, category, color, target_frequency) VALUES
(1, 'Morning Exercise', 'Daily 30-minute workout to stay fit and healthy', 'Health & Fitness', '#28a745', 'daily'),
(1, 'Read Books', 'Read for at least 20 minutes to expand knowledge', 'Education & Learning', '#17a2b8', 'daily'),
(1, 'Meditation', 'Daily mindfulness practice for mental well-being', 'Wellness & Mindfulness', '#6f42c1', 'daily'),
(1, 'Drink Water', 'Drink 8 glasses of water throughout the day', 'Health & Fitness', '#007bff', 'daily'),
(1, 'Journal Writing', 'Write daily thoughts and reflections', 'Personal Development', '#fd7e14', 'daily'),
(1, 'Learn Programming', 'Practice coding for 1 hour daily', 'Education & Learning', '#20c997', 'daily'),
(1, 'Call Family', 'Stay connected with family members', 'Social & Relationships', '#e83e8c', 'weekly'),
(1, 'Clean House', 'Weekly house cleaning and organization', 'Home & Organization', '#6c757d', 'weekly');

-- Insert sample habits for john_doe (user_id = 2)
INSERT INTO habits (user_id, title, description, category, color, target_frequency) VALUES
(2, 'Morning Run', 'Run 5km every morning', 'Health & Fitness', '#28a745', 'daily'),
(2, 'Practice Guitar', 'Practice guitar for 45 minutes', 'Hobbies & Creativity', '#ffc107', 'daily'),
(2, 'Budget Review', 'Review and update personal budget', 'Finance', '#dc3545', 'weekly');

-- Insert sample habits for jane_smith (user_id = 3)
INSERT INTO habits (user_id, title, description, category, color, target_frequency) VALUES
(3, 'Yoga Practice', 'Daily yoga session for flexibility', 'Health & Fitness', '#6f42c1', 'daily'),
(3, 'Language Learning', 'Study Spanish for 30 minutes', 'Education & Learning', '#17a2b8', 'daily'),
(3, 'Meal Prep', 'Prepare healthy meals for the week', 'Health & Fitness', '#28a745', 'weekly');

-- Insert sample tracking data for the past 30 days for demo_user
-- This creates a realistic tracking history with some missed days

SET @start_date = DATE_SUB(CURDATE(), INTERVAL 30 DAY);

-- Morning Exercise tracking (habit_id = 1)
INSERT INTO habit_tracking (habit_id, user_id, tracking_date, completed, notes, completed_at) VALUES
(1, 1, DATE_ADD(@start_date, INTERVAL 0 DAY), 1, 'Great workout session!', DATE_ADD(DATE_ADD(@start_date, INTERVAL 0 DAY), INTERVAL 7 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 1 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 1 DAY), INTERVAL 7 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 2 DAY), 0, 'Skipped due to rain', NULL),
(1, 1, DATE_ADD(@start_date, INTERVAL 3 DAY), 1, 'Indoor workout', DATE_ADD(DATE_ADD(@start_date, INTERVAL 3 DAY), INTERVAL 8 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 4 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 4 DAY), INTERVAL 7 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 5 DAY), 1, 'Felt energized!', DATE_ADD(DATE_ADD(@start_date, INTERVAL 5 DAY), INTERVAL 6 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 6 DAY), 0, 'Too tired', NULL),
(1, 1, DATE_ADD(@start_date, INTERVAL 7 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 7 DAY), INTERVAL 7 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 8 DAY), 1, 'Extended session', DATE_ADD(DATE_ADD(@start_date, INTERVAL 8 DAY), INTERVAL 7 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 9 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 9 DAY), INTERVAL 8 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 10 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 10 DAY), INTERVAL 7 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 11 DAY), 0, 'Sick day', NULL),
(1, 1, DATE_ADD(@start_date, INTERVAL 12 DAY), 0, 'Still recovering', NULL),
(1, 1, DATE_ADD(@start_date, INTERVAL 13 DAY), 1, 'Back to routine', DATE_ADD(DATE_ADD(@start_date, INTERVAL 13 DAY), INTERVAL 7 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 14 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 14 DAY), INTERVAL 7 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 15 DAY), 1, 'Great progress', DATE_ADD(DATE_ADD(@start_date, INTERVAL 15 DAY), INTERVAL 6 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 16 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 16 DAY), INTERVAL 7 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 17 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 17 DAY), INTERVAL 8 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 18 DAY), 0, 'Busy day', NULL),
(1, 1, DATE_ADD(@start_date, INTERVAL 19 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 19 DAY), INTERVAL 7 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 20 DAY), 1, 'Feeling strong', DATE_ADD(DATE_ADD(@start_date, INTERVAL 20 DAY), INTERVAL 7 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 21 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 21 DAY), INTERVAL 6 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 22 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 22 DAY), INTERVAL 7 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 23 DAY), 1, 'New personal best', DATE_ADD(DATE_ADD(@start_date, INTERVAL 23 DAY), INTERVAL 7 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 24 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 24 DAY), INTERVAL 8 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 25 DAY), 0, 'Travel day', NULL),
(1, 1, DATE_ADD(@start_date, INTERVAL 26 DAY), 1, 'Hotel gym workout', DATE_ADD(DATE_ADD(@start_date, INTERVAL 26 DAY), INTERVAL 9 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 27 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 27 DAY), INTERVAL 7 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 28 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 28 DAY), INTERVAL 7 HOUR)),
(1, 1, DATE_ADD(@start_date, INTERVAL 29 DAY), 1, 'Consistent progress', DATE_ADD(DATE_ADD(@start_date, INTERVAL 29 DAY), INTERVAL 6 HOUR));

-- Read Books tracking (habit_id = 2)
INSERT INTO habit_tracking (habit_id, user_id, tracking_date, completed, notes, completed_at) VALUES
(2, 1, DATE_ADD(@start_date, INTERVAL 0 DAY), 1, 'Started new novel', DATE_ADD(DATE_ADD(@start_date, INTERVAL 0 DAY), INTERVAL 20 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 1 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 1 DAY), INTERVAL 21 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 2 DAY), 1, 'Interesting chapter', DATE_ADD(DATE_ADD(@start_date, INTERVAL 2 DAY), INTERVAL 19 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 3 DAY), 0, 'Too busy', NULL),
(2, 1, DATE_ADD(@start_date, INTERVAL 4 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 4 DAY), INTERVAL 22 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 5 DAY), 1, 'Great plot twist', DATE_ADD(DATE_ADD(@start_date, INTERVAL 5 DAY), INTERVAL 20 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 6 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 6 DAY), INTERVAL 21 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 7 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 7 DAY), INTERVAL 19 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 8 DAY), 0, 'Forgot to read', NULL),
(2, 1, DATE_ADD(@start_date, INTERVAL 9 DAY), 1, 'Caught up', DATE_ADD(DATE_ADD(@start_date, INTERVAL 9 DAY), INTERVAL 20 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 10 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 10 DAY), INTERVAL 21 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 11 DAY), 1, 'Finished book!', DATE_ADD(DATE_ADD(@start_date, INTERVAL 11 DAY), INTERVAL 22 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 12 DAY), 1, 'Started new book', DATE_ADD(DATE_ADD(@start_date, INTERVAL 12 DAY), INTERVAL 20 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 13 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 13 DAY), INTERVAL 19 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 14 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 14 DAY), INTERVAL 21 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 15 DAY), 0, 'Busy weekend', NULL),
(2, 1, DATE_ADD(@start_date, INTERVAL 16 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 16 DAY), INTERVAL 20 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 17 DAY), 1, 'Learning a lot', DATE_ADD(DATE_ADD(@start_date, INTERVAL 17 DAY), INTERVAL 21 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 18 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 18 DAY), INTERVAL 19 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 19 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 19 DAY), INTERVAL 20 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 20 DAY), 1, 'Great insights', DATE_ADD(DATE_ADD(@start_date, INTERVAL 20 DAY), INTERVAL 22 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 21 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 21 DAY), INTERVAL 21 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 22 DAY), 0, 'Tired', NULL),
(2, 1, DATE_ADD(@start_date, INTERVAL 23 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 23 DAY), INTERVAL 20 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 24 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 24 DAY), INTERVAL 19 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 25 DAY), 1, 'Travel reading', DATE_ADD(DATE_ADD(@start_date, INTERVAL 25 DAY), INTERVAL 14 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 26 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 26 DAY), INTERVAL 21 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 27 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 27 DAY), INTERVAL 20 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 28 DAY), 1, 'Almost done', DATE_ADD(DATE_ADD(@start_date, INTERVAL 28 DAY), INTERVAL 22 HOUR)),
(2, 1, DATE_ADD(@start_date, INTERVAL 29 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 29 DAY), INTERVAL 21 HOUR));

-- Meditation tracking (habit_id = 3)
INSERT INTO habit_tracking (habit_id, user_id, tracking_date, completed, notes, completed_at) VALUES
(3, 1, DATE_ADD(@start_date, INTERVAL 0 DAY), 1, '10 minutes of mindfulness', DATE_ADD(DATE_ADD(@start_date, INTERVAL 0 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 1 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 1 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 2 DAY), 0, 'Rushed morning', NULL),
(3, 1, DATE_ADD(@start_date, INTERVAL 3 DAY), 1, 'Felt peaceful', DATE_ADD(DATE_ADD(@start_date, INTERVAL 3 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 4 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 4 DAY), INTERVAL 7 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 5 DAY), 1, 'Extended session', DATE_ADD(DATE_ADD(@start_date, INTERVAL 5 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 6 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 6 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 7 DAY), 0, 'Overslept', NULL),
(3, 1, DATE_ADD(@start_date, INTERVAL 8 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 8 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 9 DAY), 1, 'Very relaxing', DATE_ADD(DATE_ADD(@start_date, INTERVAL 9 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 10 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 10 DAY), INTERVAL 7 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 11 DAY), 0, 'Sick', NULL),
(3, 1, DATE_ADD(@start_date, INTERVAL 12 DAY), 0, 'Still sick', NULL),
(3, 1, DATE_ADD(@start_date, INTERVAL 13 DAY), 1, 'Gentle return', DATE_ADD(DATE_ADD(@start_date, INTERVAL 13 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 14 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 14 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 15 DAY), 1, 'Clear mind', DATE_ADD(DATE_ADD(@start_date, INTERVAL 15 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 16 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 16 DAY), INTERVAL 7 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 17 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 17 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 18 DAY), 0, 'Busy morning', NULL),
(3, 1, DATE_ADD(@start_date, INTERVAL 19 DAY), 1, 'Evening session', DATE_ADD(DATE_ADD(@start_date, INTERVAL 19 DAY), INTERVAL 18 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 20 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 20 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 21 DAY), 1, 'Focused session', DATE_ADD(DATE_ADD(@start_date, INTERVAL 21 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 22 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 22 DAY), INTERVAL 7 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 23 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 23 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 24 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 24 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 25 DAY), 0, 'Travel day', NULL),
(3, 1, DATE_ADD(@start_date, INTERVAL 26 DAY), 1, 'Hotel meditation', DATE_ADD(DATE_ADD(@start_date, INTERVAL 26 DAY), INTERVAL 7 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 27 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 27 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 28 DAY), 1, 'Peaceful morning', DATE_ADD(DATE_ADD(@start_date, INTERVAL 28 DAY), INTERVAL 6 HOUR)),
(3, 1, DATE_ADD(@start_date, INTERVAL 29 DAY), 1, NULL, DATE_ADD(DATE_ADD(@start_date, INTERVAL 29 DAY), INTERVAL 6 HOUR));

-- Add some tracking data for other habits with less frequency
-- Drink Water tracking (habit_id = 4) - high completion rate
INSERT INTO habit_tracking (habit_id, user_id, tracking_date, completed, notes, completed_at) 
SELECT 4, 1, DATE_ADD(@start_date, INTERVAL seq DAY), 
       CASE WHEN RAND() > 0.15 THEN 1 ELSE 0 END,
       CASE WHEN RAND() > 0.7 THEN 'Stayed hydrated' ELSE NULL END,
       CASE WHEN RAND() > 0.15 THEN DATE_ADD(DATE_ADD(@start_date, INTERVAL seq DAY), INTERVAL 18 HOUR) ELSE NULL END
FROM (
    SELECT 0 as seq UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION 
    SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION 
    SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION 
    SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION 
    SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION 
    SELECT 25 UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29
) AS numbers;

-- Journal Writing tracking (habit_id = 5) - moderate completion rate
INSERT INTO habit_tracking (habit_id, user_id, tracking_date, completed, notes, completed_at) 
SELECT 5, 1, DATE_ADD(@start_date, INTERVAL seq DAY), 
       CASE WHEN RAND() > 0.35 THEN 1 ELSE 0 END,
       CASE WHEN RAND() > 0.8 THEN 'Reflective writing' ELSE NULL END,
       CASE WHEN RAND() > 0.35 THEN DATE_ADD(DATE_ADD(@start_date, INTERVAL seq DAY), INTERVAL 21 HOUR) ELSE NULL END
FROM (
    SELECT 0 as seq UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION 
    SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION 
    SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION 
    SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION 
    SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION 
    SELECT 25 UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29
) AS numbers;

-- Learn Programming tracking (habit_id = 6) - moderate completion rate
INSERT INTO habit_tracking (habit_id, user_id, tracking_date, completed, notes, completed_at) 
SELECT 6, 1, DATE_ADD(@start_date, INTERVAL seq DAY), 
       CASE WHEN RAND() > 0.4 THEN 1 ELSE 0 END,
       CASE WHEN RAND() > 0.75 THEN 'Good progress' ELSE NULL END,
       CASE WHEN RAND() > 0.4 THEN DATE_ADD(DATE_ADD(@start_date, INTERVAL seq DAY), INTERVAL 19 HOUR) ELSE NULL END
FROM (
    SELECT 0 as seq UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION 
    SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION 
    SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION 
    SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION 
    SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION 
    SELECT 25 UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29
) AS numbers;

-- Add some weekly habit tracking
-- Call Family (habit_id = 7) - weekly habit
INSERT INTO habit_tracking (habit_id, user_id, tracking_date, completed, notes, completed_at) VALUES
(7, 1, DATE_ADD(@start_date, INTERVAL 6 DAY), 1, 'Long chat with mom', DATE_ADD(DATE_ADD(@start_date, INTERVAL 6 DAY), INTERVAL 15 HOUR)),
(7, 1, DATE_ADD(@start_date, INTERVAL 13 DAY), 0, 'Too busy this week', NULL),
(7, 1, DATE_ADD(@start_date, INTERVAL 20 DAY), 1, 'Video call with siblings', DATE_ADD(DATE_ADD(@start_date, INTERVAL 20 DAY), INTERVAL 16 HOUR)),
(7, 1, DATE_ADD(@start_date, INTERVAL 27 DAY), 1, 'Family dinner call', DATE_ADD(DATE_ADD(@start_date, INTERVAL 27 DAY), INTERVAL 17 HOUR));

-- Clean House (habit_id = 8) - weekly habit
INSERT INTO habit_tracking (habit_id, user_id, tracking_date, completed, notes, completed_at) VALUES
(8, 1, DATE_ADD(@start_date, INTERVAL 5 DAY), 1, 'Deep cleaned kitchen', DATE_ADD(DATE_ADD(@start_date, INTERVAL 5 DAY), INTERVAL 10 HOUR)),
(8, 1, DATE_ADD(@start_date, INTERVAL 12 DAY), 1, 'Organized closet', DATE_ADD(DATE_ADD(@start_date, INTERVAL 12 DAY), INTERVAL 11 HOUR)),
(8, 1, DATE_ADD(@start_date, INTERVAL 19 DAY), 0, 'Skipped - was sick', NULL),
(8, 1, DATE_ADD(@start_date, INTERVAL 26 DAY), 1, 'Full house cleaning', DATE_ADD(DATE_ADD(@start_date, INTERVAL 26 DAY), INTERVAL 9 HOUR));

-- Add some tracking data for other users to make the demo more realistic

-- John Doe's habits tracking
INSERT INTO habit_tracking (habit_id, user_id, tracking_date, completed, notes, completed_at) 
SELECT 9, 2, DATE_ADD(@start_date, INTERVAL seq DAY), 
       CASE WHEN RAND() > 0.25 THEN 1 ELSE 0 END,
       CASE WHEN RAND() > 0.8 THEN 'Great run!' ELSE NULL END,
       CASE WHEN RAND() > 0.25 THEN DATE_ADD(DATE_ADD(@start_date, INTERVAL seq DAY), INTERVAL 6 HOUR) ELSE NULL END
FROM (
    SELECT 0 as seq UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION 
    SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION 
    SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION 
    SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION 
    SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION 
    SELECT 25 UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29
) AS numbers;

-- Jane Smith's habits tracking
INSERT INTO habit_tracking (habit_id, user_id, tracking_date, completed, notes, completed_at) 
SELECT 12, 3, DATE_ADD(@start_date, INTERVAL seq DAY), 
       CASE WHEN RAND() > 0.3 THEN 1 ELSE 0 END,
       CASE WHEN RAND() > 0.85 THEN 'Relaxing session' ELSE NULL END,
       CASE WHEN RAND() > 0.3 THEN DATE_ADD(DATE_ADD(@start_date, INTERVAL seq DAY), INTERVAL 7 HOUR) ELSE NULL END
FROM (
    SELECT 0 as seq UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION 
    SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION 
    SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION 
    SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION 
    SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION 
    SELECT 25 UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29
) AS numbers;

