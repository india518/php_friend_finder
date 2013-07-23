SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `friend_finder` ;
CREATE SCHEMA IF NOT EXISTS `friend_finder` DEFAULT CHARACTER SET latin1 ;
USE `friend_finder` ;

-- -----------------------------------------------------
-- Table `friend_finder`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `friend_finder`.`users` ;

CREATE  TABLE IF NOT EXISTS `friend_finder`.`users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `first_name` VARCHAR(100) NOT NULL ,
  `last_name` VARCHAR(100) NOT NULL ,
  `email` VARCHAR(100) NOT NULL ,
  `password` VARCHAR(250) NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  `updated_at` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `friend_finder`.`friends`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `friend_finder`.`friends` ;

CREATE  TABLE IF NOT EXISTS `friend_finder`.`friends` (
  `user_id` INT(11) NOT NULL ,
  `friend_id` INT(11) NOT NULL ,
  INDEX `fk_friends_users_idx` (`user_id` ASC) ,
  INDEX `fk_friends_users1_idx` (`friend_id` ASC) ,
  CONSTRAINT `fk_friends_users`
    FOREIGN KEY (`user_id` )
    REFERENCES `friend_finder`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_friends_users1`
    FOREIGN KEY (`friend_id` )
    REFERENCES `friend_finder`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;

USE `friend_finder` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
