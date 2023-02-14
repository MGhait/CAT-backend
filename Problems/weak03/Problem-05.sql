/*
You are given three tables: Students, Friends and Packages.
 Students contains two columns: ID and Name.
  Friends contains two columns: ID and Friend_ID (ID of the ONLY best friend).
 Packages contains two columns: ID and Salary (offered salary in $ thousands per month).
*/


SELECT name
FROM students  AS s 
JOIN Friends AS f
ON s.ID = f.id
JOIN Packages AS p
ON s.ID = p.ID
WHERE p.salary < (SELECT salary FROM Packages WHERE ID = f.Friend_ID)
ORDER BY (SELECT salary FROM Packages WHERE ID = f.Friend_ID) ASC;