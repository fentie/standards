-- drop table if exists users;
-- drop table if exists shifts;

CREATE TABLE users(
  id INTEGER PRIMARY KEY,
  name varchar,
  email varchar,
  phone varchar,
  role varchar,
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shifts(
  id INTEGER PRIMARY KEY,
  manager_id int NOT NULL,
  employee_id int,
  break float,
  start_time datetime NOT NULL,
  end_time datetime NOT NULL,
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(manager_id) REFERENCES users(id),
  FOREIGN KEY(employee_id) REFERENCES users(id)
);
