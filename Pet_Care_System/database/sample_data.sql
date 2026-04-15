INSERT INTO users (username,email,password,role) VALUES
('admin1','admin@example.com','hashedpass','admin'),
('vet1','vet@example.com','hashedpass','vet'),
('owner1','owner@example.com','hashedpass','owner');

INSERT INTO pets (owner_id,name,species,breed,dob) VALUES
(3,'Buddy','Dog','Labrador','2023-01-15'),
(3,'Kitty','Cat','Persian','2022-05-10');

INSERT INTO vaccinations (pet_id,vaccine_name,date,status) VALUES
(1,'Rabies','2025-11-20','Pending'),
(2,'Distemper','2025-09-15','Completed');
