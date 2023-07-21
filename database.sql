CREATE DATABASE cp476;

USE cp476;

CREATE TABLE suppliers(
    supplierID INT PRIMARY KEY, 
    supplierName VARCHAR(255),
    addr VARCHAR(255),
    phone VARCHAR(50),
    email VARCHAR(255)
);

CREATE TABLE products(
    productID INT,
    productName VARCHAR(255),
    productDescription VARCHAR(255),
    price DECIMAL(10,2),
    quantity INT,
    productStatus VARCHAR(5),
    supplierID INT,
    FOREIGN KEY (supplierID) REFERENCES suppliers(supplierID),
    PRIMARY KEY (productID, SupplierID)
);

CREATE TABLE inventory(
    productID INT,
    productName VARCHAR(255),
    quantity INT,
    price DECIMAL(10,2),
    productStatus VARCHAR(5),
    supplierName VARCHAR(255)
);