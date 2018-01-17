
# User
CREATE TABLE tx_feuseradd_domain_model_user (
  uid INT(11) NOT NULL AUTO_INCREMENT,
  pid INT(11) NOT NULL DEFAULT '0',

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,

  first_name VARCHAR(255) NOT NULL DEFAULT '',
  last_name VARCHAR(255) NOT NULL DEFAULT '',
  email VARCHAR(255) NOT NULL DEFAULT '',
  company VARCHAR(255) NOT NULL DEFAULT '',
  position VARCHAR(255) NOT NULL DEFAULT '',
  password VARCHAR(255) NOT NULL DEFAULT '',

  registration_state CHAR(10) NOT NULL DEFAULT '',

  PRIMARY KEY (uid)
);
