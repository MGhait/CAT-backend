CREATE DATABASE Small_business;
USE small_business;
CREATE TABLE employees (
    emp_id INT PRIMARY KEY,
    email VARCHAR(30),
    name VARCHAR(30),
    salary INT ,
    department_id INT 
);

CREATE TABLE department (
    id INT PRIMARY KEY ,
    name VARCHAR(30)
);

ALTER TABLE employees ADD FOREIGN KEY (department_id) REFERENCES department(id) ON DELETE SET NULL;

CREATE TABLE admins(
    admin_id INT PRIMARY KEY,
    email VARCHAR(30),
    admin_password VARCHAR(30),
    employee_id INT ,
    FOREIGN KEY (employee_id) REFERENCES employees(emp_id)
);


CREATE TABLE customer (
    customer_id INT PRIMARY KEY,
    full_name VARCHAR(60),
    address VARCHAR(60),
    Phone VARCHAR(60),
    age VARCHAR(3),
    gender VARCHAR(6),
    customer_password VARCHAR(60)
);

CREATE TABLE products (
    product_id INT PRIMARY KEY,
    product_name VARCHAR(50),
    product_category VARCHAR(30),
    price INT 
    
);

CREATE TABLE orders (
    order_id INT PRIMARY KEY ,
    customer_id INT ,
    product_id INT ,
    order_date DATE,
    order_amount INT ,
    FOREIGN KEY (customer_id) REFERENCES customer(customer_id) ON DELETE SET NULL ,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE SET NULL
);