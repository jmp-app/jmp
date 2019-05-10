-- -----------------------------------------------------
-- Schema jmp
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `jmp`;

CREATE SCHEMA IF NOT EXISTS `jmp` DEFAULT CHARACTER SET utf8;
USE `jmp`;

-- -----------------------------------------------------
-- Table `jmp`.`event_type`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `jmp`.`event_type`
(
    `id`    INT(11)     NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(50) NOT NULL,
    `color` VARCHAR(25) NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `jmp`.`registration_state`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `jmp`.`registration_state`
(
    `id`              INT(11)      NOT NULL AUTO_INCREMENT,
    `name`            VARCHAR(255) NOT NULL,
    `reason_required` TINYINT      NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `jmp`.`event`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `jmp`.`event`
(
    `id`                            INT(11)     NOT NULL AUTO_INCREMENT,
    `title`                         VARCHAR(50) NOT NULL,
    `from`                          DATETIME    NOT NULL,
    `to`                            DATETIME    NOT NULL,
    `place`                         VARCHAR(50) NULL DEFAULT NULL,
    `description`                   TEXT        NULL DEFAULT NULL,
    `event_type_id`                 INT(11)     NOT NULL,
    `default_registration_state_id` INT(11)     NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_event_event_kind1`
        FOREIGN KEY
            (
             `event_type_id`
                )
            REFERENCES `jmp`.`event_type`
                (
                 `id`
                    )
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_registration_state1`
    FOREIGN KEY (`default_registration_state_id`)
        REFERENCES `jmp`.`registration_state` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 29
    DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `jmp`.`group`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `jmp`.`group`
(
    `id`   INT(11)     NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(45) NOT NULL UNIQUE,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 5
    DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `jmp`.`event_has_group`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `jmp`.`event_has_group`
(
  `event_id` INT(11) NOT NULL,
  `group_id` INT(11) NOT NULL,
  PRIMARY KEY (`event_id`, `group_id`),
  -- INDEX `fk_event_has_group_group1_idx` (`group_id` ASC) VISIBLE,
  -- -- INDEX `fk_event_has_group_event1_idx` (`event_id` ASC) VISIBLE,
  CONSTRAINT `fk_event_has_group_event1`
    FOREIGN KEY (`event_id`)
        REFERENCES `jmp`.`event` (`id`)
        ON DELETE CASCADE
        ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_has_group_group1`
    FOREIGN KEY (`group_id`)
        REFERENCES `jmp`.`group` (`id`)
      ON DELETE CASCADE
        ON UPDATE NO ACTION
)
    ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `jmp`.`user`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `jmp`.`user`
(
    `id`              INT(11)           NOT NULL AUTO_INCREMENT,
    `username`        VARCHAR(101)      NOT NULL UNIQUE,
    `lastname`        VARCHAR(50),
    `firstname`       VARCHAR(50),
    `email`           VARCHAR(255),
    `password`        VARCHAR(255)      NOT NULL,
    `password_change` TINYINT DEFAULT 0 NOT NULL,
    `is_admin`        TINYINT DEFAULT 0 NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 160
    DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `jmp`.`membership`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `jmp`.`membership`
(
    `group_id` INT(11) NOT NULL,
    `user_id`  INT(11) NOT NULL,
    PRIMARY KEY (`group_id`, `user_id`),
  -- -- -- INDEX `fk_group_has_user_user1_idx` (`user_id` ASC) VISIBLE,
  -- -- INDEX `fk_group_has_user_group_idx` (`group_id` ASC) VISIBLE,
    CONSTRAINT `fk_group_has_user_group`
    FOREIGN KEY (`group_id`)
        REFERENCES `jmp`.`group` (`id`)
      ON DELETE CASCADE
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_group_has_user_user1`
    FOREIGN KEY (`user_id`)
        REFERENCES `jmp`.`user` (`id`)
      ON DELETE CASCADE
        ON UPDATE NO ACTION
)
    ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `jmp`.`registration`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `jmp`.`registration`
(
    `event_id`              INT(11)     NOT NULL,
    `user_id`               INT(11)     NOT NULL,
    `registration_state_id` INT(11)     NOT NULL,
    `reason`                VARCHAR(80) NULL DEFAULT NULL,
  PRIMARY KEY (`event_id`, `user_id`),
  CONSTRAINT `fk_presence_event1`
    FOREIGN KEY (`event_id`)
        REFERENCES `jmp`.`event` (`id`)
        ON DELETE CASCADE
        ON UPDATE NO ACTION,
  CONSTRAINT `fk_presence_user1`
    FOREIGN KEY (`user_id`)
        REFERENCES `jmp`.`user` (`id`)
      ON DELETE CASCADE
        ON UPDATE NO ACTION,
  CONSTRAINT `fk_registration_registration_state1`
    FOREIGN KEY (`registration_state_id`)
        REFERENCES `jmp`.`registration_state` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
    ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `jmp`.`presence`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `jmp`.`presence`
(
    `event_id`     INT(11) NOT NULL,
    `user_id`      INT(11) NOT NULL,
    `auditor_id`   INT(11) NOT NULL,
    `has_attended` TINYINT NOT NULL,
    PRIMARY KEY (`event_id`, `user_id`, `auditor_id`),
    CONSTRAINT `presence_actually_ibfk_1`
    FOREIGN KEY (`event_id`)
        REFERENCES `jmp`.`event` (`id`)
        ON DELETE CASCADE
        ON UPDATE NO ACTION,
    CONSTRAINT `presence_actually_ibfk_2`
    FOREIGN KEY (`user_id`)
      REFERENCES `jmp`.`user` (`id`)
      ON DELETE CASCADE,
    CONSTRAINT `presence_actually_ibfk_3`
        FOREIGN KEY (`auditor_id`)
      REFERENCES `jmp`.`user` (`id`)
      ON DELETE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table `jmp`.`user_meta_type`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `jmp`.`user_meta_type`
(
    `id`   INT(11)      NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `jmp`.`user_meta`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `jmp`.`user_meta`
(
    `id`                INT(11)      NOT NULL AUTO_INCREMENT,
    `user_id`           INT(11)      NOT NULL,
    `user_meta_type_id` INT(11)      NOT NULL,
    `value`             VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_user_has_user_meta_type_user1`
    FOREIGN KEY (`user_id`)
        REFERENCES `jmp`.`user` (`id`)
      ON DELETE CASCADE
        ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_user_meta_type_user_meta_type1`
    FOREIGN KEY (`user_meta_type_id`)
        REFERENCES `jmp`.`user_meta_type` (`id`)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
    ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `jmp`.`registration_state`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `jmp`.`registration_state`
(
    `id`              INT(11)      NOT NULL AUTO_INCREMENT,
    `name`            VARCHAR(255) NOT NULL,
    `reason_required` TINYINT      NOT NULL,
    PRIMARY KEY
        (
         `id`
            )
)
  ENGINE = InnoDB;
