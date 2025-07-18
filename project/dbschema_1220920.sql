
CREATE TABLE users (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'owner', 'manager') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE customers (
    customerId INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    nationalId VARCHAR(20) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255),
    dateOfBirth DATE,
    mobile VARCHAR(20),
    telephone VARCHAR(20),
    FOREIGN KEY (userId) REFERENCES users(userId) ON DELETE CASCADE
);


CREATE TABLE owners (
    ownerId INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    nationalId VARCHAR(20) NOT NULL,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255),
    dateOfBirth DATE,
    mobile VARCHAR(20),
    telephone VARCHAR(20),
    bankName VARCHAR(100),
    bankBranch VARCHAR(100),
    bankAccountNumber VARCHAR(50),
    FOREIGN KEY (userId) REFERENCES users(userId) ON DELETE CASCADE
);

CREATE TABLE flats (
    flatId INT AUTO_INCREMENT PRIMARY KEY,
    ownerId INT NOT NULL,
    flatRefNo VARCHAR(10) UNIQUE,
    location VARCHAR(100),
    address VARCHAR(255),
    monthlyRent DECIMAL(10,2),
    availableFrom DATE,
    bedrooms INT,
    bathrooms INT,
    furnished BOOLEAN DEFAULT FALSE,
    sizeSqm INT,
    heating BOOLEAN DEFAULT FALSE,
    airConditioning BOOLEAN DEFAULT FALSE,
    accessControl BOOLEAN DEFAULT FALSE,
    parking BOOLEAN DEFAULT FALSE,
    backyard ENUM('individual', 'shared', 'none') DEFAULT 'none',
    playground BOOLEAN DEFAULT FALSE,
    storage BOOLEAN DEFAULT FALSE,
    approved ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (ownerId) REFERENCES owners(ownerId) ON DELETE CASCADE
);


CREATE TABLE flat_photos (
    photoId INT AUTO_INCREMENT PRIMARY KEY,
    flatId INT NOT NULL,
    photoUrl VARCHAR(255) NOT NULL,
    FOREIGN KEY (flatId) REFERENCES flats(flatId) ON DELETE CASCADE
);


CREATE TABLE marketing_info (
    marketingId INT AUTO_INCREMENT PRIMARY KEY,
    flatId INT NOT NULL,
    title VARCHAR(100),
    description TEXT,
    url VARCHAR(255),
    FOREIGN KEY (flatId) REFERENCES flats(flatId) ON DELETE CASCADE
);


CREATE TABLE rentals (
    rentalId INT AUTO_INCREMENT PRIMARY KEY,
    flatId INT NOT NULL,
    customerId INT NOT NULL,
    rentStartDate DATE NOT NULL,
    rentEndDate DATE NOT NULL,
    totalAmount DECIMAL(10,2),
    paymentStatus ENUM('pending', 'confirmed','rejected') DEFAULT 'pending',
    FOREIGN KEY (flatId) REFERENCES flats(flatId),
    FOREIGN KEY (customerId) REFERENCES customers(customerId)
);


CREATE TABLE appointments (
    appointmentId INT AUTO_INCREMENT PRIMARY KEY,
    flatId INT NOT NULL,
    customerId INT NULL,
    phone VARCHAR(30),
    ownerId INT NOT NULL,
    appointmentDate DATETIME NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (flatId) REFERENCES flats(flatId),
    FOREIGN KEY (customerId) REFERENCES customers(customerId),
    FOREIGN KEY (ownerId) REFERENCES owners(ownerId)
);


CREATE TABLE messages (
    messageId INT AUTO_INCREMENT PRIMARY KEY,
    senderId INT NOT NULL,
    receiverId INT NOT NULL,
    messageTitle VARCHAR(100),
    messageBody TEXT,
    messageDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    isRead BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (senderId) REFERENCES users(userId),
    FOREIGN KEY (receiverId) REFERENCES users(userId)
);
