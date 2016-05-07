DELETE FROM users;
DELETE FROM shifts;

INSERT INTO users(id, name, email, phone, role)
VALUES (1, 'Carl', 'carl@example.com', '123456790', 'employee'),
  (2, 'Jane', 'jane@example.com', '123456791', 'manager'),
  (3, 'Steve', 'steve@example.com', '123456792', 'employee'),
  (4, 'Becky', 'becky@example.com', '123456793', 'employee');

INSERT INTO shifts(manager_id, employee_id, break, start_time, end_time)
VALUES (2, 1, 4.0, '2016-05-01 15:00:00', '2016-05-01 22:00:00'),
  (2, NULL, 4.0, '2016-05-02 15:00:00', '2016-05-02 22:00:00'),
  (2, 1, 4.0, '2016-05-02 15:00:00', '2016-05-02 16:00:00'),
  (2, 3, 4.0, '2016-05-01 15:00:00', '2016-05-01 22:00:00'),
  (2, NULL, 4.0, '2016-05-02 15:00:00', '2016-05-02 16:00:00'),
  (2, 4, 4.0, '2016-05-01 15:00:00', '2016-05-01 22:00:00');
