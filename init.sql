CREATE TABLE Usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    clave VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    activo BOOLEAN DEFAULT TRUE
);

CREATE TABLE Roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE Servicios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    tipo VARCHAR(100),
    precio DECIMAL(10, 2) NOT NULL,
    activo BOOLEAN DEFAULT TRUE
);

CREATE TABLE Productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    activo BOOLEAN DEFAULT TRUE
);

CREATE TABLE Novedades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    foto LONGBLOB,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE
);

CREATE TABLE Clientes (
    id INT PRIMARY KEY,
    usuarioId INT NOT NULL,
    ciudad VARCHAR(100),
    telefono VARCHAR(20),
    direccion VARCHAR(255),
    FOREIGN KEY (usuarioId) REFERENCES Usuarios(id) ON DELETE CASCADE
);

CREATE TABLE Personal (
    id INT PRIMARY KEY,
    usuarioId INT NOT NULL,
    rolId INT NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (usuarioId) REFERENCES Usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (rolId) REFERENCES Roles(id) ON DELETE RESTRICT
);

CREATE TABLE Mascotas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clienteId INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    foto LONGBLOB,
    raza VARCHAR(100),
    color VARCHAR(50),
    fechaDeNac DATE,
    fechaMuerte DATE,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (clienteId) REFERENCES Clientes(id) ON DELETE CASCADE
);

CREATE TABLE RolesServicios (
    rolId INT NOT NULL,
    servicioId INT NOT NULL,
    PRIMARY KEY (rolId, servicioId),
    FOREIGN KEY (rolId) REFERENCES Roles(id) ON DELETE CASCADE,
    FOREIGN KEY (servicioId) REFERENCES Servicios(id) ON DELETE CASCADE
);

CREATE TABLE Atenciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clienteId INT NOT NULL,
    mascotaId INT NOT NULL,
    personalId INT NOT NULL,
    fechaHora DATETIME NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    servicioId INT NOT NULL,
    descripcion TEXT,
    FOREIGN KEY (servicioId) REFERENCES Servicios(id) ON DELETE RESTRICT,
    FOREIGN KEY (clienteId) REFERENCES Clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (mascotaId) REFERENCES Mascotas(id) ON DELETE CASCADE,
    FOREIGN KEY (personalId) REFERENCES Personal(id) ON DELETE RESTRICT
);