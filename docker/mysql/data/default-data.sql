CREATE TABLE publications (
  externalId VARCHAR(50) NOT NULL,
  title VARCHAR(1024) NOT NULL,
  year CHAR(50) NOT NULL,
  type varchar(50) NOT NULL,
  poster VARCHAR(1024) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(externalId),
  CONSTRAINT unique_externalId UNIQUE (externalId)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;