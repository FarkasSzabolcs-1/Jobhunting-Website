CREATE TABLE jobs (
                      id              INT AUTO_INCREMENT PRIMARY KEY,
                      title           VARCHAR(150) NOT NULL,
                      company         VARCHAR(100) NOT NULL,
                      position        VARCHAR(100) NOT NULL,
                      category        VARCHAR(80)  NULL,
                      city            VARCHAR(80)  NULL,
                      salary          FLOAT  NULL,
                      description     TEXT         NOT NULL,
                      is_active       TINYINT(1) DEFAULT 1,
                      created_by      VARCHAR(50) NULL
)

CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(50) NOT NULL UNIQUE,
                       password VARCHAR(255) NOT NULL,
                       email VARCHAR(100) NOT NULL UNIQUE,
                       cv VARCHAR(255) NULL
);

CREATE TABLE job_applications (
                                  id              INT AUTO_INCREMENT PRIMARY KEY,
                                  job_id          INT NOT NULL,
                                  user_id         INT NOT NULL,
                                  applied_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
                                  status          ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',

                                  UNIQUE KEY unique_application (job_id, user_id),

                                  FOREIGN KEY (job_id)  REFERENCES jobs(id)  ON DELETE CASCADE,
                                  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

);
