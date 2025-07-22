-- Create Students table
CREATE TABLE Students (
    StudentID INT PRIMARY KEY,
    FirstName VARCHAR(50),
    LastName VARCHAR(50)
);

-- Create Courses table with foreign key relationship
CREATE TABLE Courses (
    CourseID INT PRIMARY KEY,
    CourseName VARCHAR(100),
    StudentID INT,
    FOREIGN KEY (StudentID) REFERENCES Students(StudentID)
);

-- Insert 4 records into Students table
INSERT INTO Students (StudentID, FirstName, LastName) VALUES
(1, 'John', 'Doe'),
(2, 'Jane', 'Smith'),
(3, 'Michael', 'Johnson'),
(4, 'Emily', 'Williams');

-- Insert 4 records into Courses table
INSERT INTO Courses (CourseID, CourseName, StudentID) VALUES
(101, 'Mathematics', 1),
(102, 'Physics', 2),
(103, 'Chemistry', 3),
(104, 'Biology', 1),
(105, 'Computer Science', 4);

-- Try deleting a student record
DELETE FROM Students WHERE StudentID = 1;