DROP TABLE IF EXISTS crmComments;
CREATE TABLE crmComments (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  contactId int(11) DEFAULT NULL,
  `comment` text,
  `date` date DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  createdOn date DEFAULT NULL,
  createdBy varchar(40) DEFAULT NULL,
  lastChange date DEFAULT NULL,
  lastChangeBy varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY contactId (contactId,`date`),
  KEY `date` (`date`)
);




DROP TABLE IF EXISTS crmContactTags;
CREATE TABLE crmContactTags (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  contactId int(11) DEFAULT NULL,
  tagId int(11) DEFAULT NULL,
  createdOn date DEFAULT NULL,
  createdBy varchar(40) DEFAULT NULL,
  lastChange date DEFAULT NULL,
  lastChangeBy varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY contactId (contactId,tagId)
);




DROP TABLE IF EXISTS crmContacts;
CREATE TABLE crmContacts (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  orgId int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  trafficSource varchar(255) DEFAULT NULL,
  campaign varchar(255) DEFAULT NULL,
  firstName varchar(100) DEFAULT NULL,
  lastName varchar(100) DEFAULT NULL,
  email varchar(255) DEFAULT NULL,
  company varchar(100) DEFAULT NULL,
  jobTitle varchar(100) DEFAULT NULL,
  address varchar(255) DEFAULT NULL,
  city varchar(100) DEFAULT NULL,
  state varchar(60) DEFAULT NULL,
  zip varchar(12) DEFAULT NULL,
  countryCode varchar(6) DEFAULT NULL,
  phone varchar(40) DEFAULT NULL,
  quali int(11) DEFAULT NULL,
  phone2 varchar(40) DEFAULT NULL,
  quali2 int(11) DEFAULT NULL,
  assignedId int(11) DEFAULT NULL,
  priority varchar(40) DEFAULT NULL,
  lastTouch datetime DEFAULT NULL,
  createdOn date DEFAULT NULL,
  createdBy varchar(40) DEFAULT NULL,
  lastChange date DEFAULT NULL,
  lastChangeBy varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY company (company),
  KEY orgId (orgId),
  KEY campaign (campaign),
  KEY trafficSource (trafficSource)
);




DROP TABLE IF EXISTS crmContactsIndex;
CREATE TABLE crmContactsIndex (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  orgId int(11) DEFAULT NULL,
  contactId int(11) DEFAULT NULL,
  `text` text,
  sha1 varchar(255) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  touchEpoc int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY sha1 (sha1),
  UNIQUE KEY contactId (contactId)
);




DROP TABLE IF EXISTS crmLandings;
CREATE TABLE crmLandings (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  userId int(11) DEFAULT NULL,
  url varchar(255) DEFAULT NULL,
  createdOn date DEFAULT NULL,
  createdBy varchar(40) DEFAULT NULL,
  lastChange date DEFAULT NULL,
  lastChangeBy varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY userId (userId)
);




DROP TABLE IF EXISTS crmOrgs;
CREATE TABLE crmOrgs (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  org varchar(255) DEFAULT NULL,
  createdOn date DEFAULT NULL,
  createdBy varchar(40) DEFAULT NULL,
  lastChange date DEFAULT NULL,
  lastChangeBy varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY org (org)
);


INSERT INTO crmOrgs (id, org, createdOn, createdBy, lastChange, lastChangeBy) VALUES (1,'theora.com','2012-11-30','ohad','2012-11-30','ohad');


DROP TABLE IF EXISTS crmPriorities;
CREATE TABLE crmPriorities (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  priority varchar(20) DEFAULT NULL,
  orderBy int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);


INSERT INTO crmPriorities (id, priority, orderBy) VALUES (1,'lower',1);
INSERT INTO crmPriorities (id, priority, orderBy) VALUES (2,'low',2);
INSERT INTO crmPriorities (id, priority, orderBy) VALUES (3,'medium',3);
INSERT INTO crmPriorities (id, priority, orderBy) VALUES (4,'high',4);
INSERT INTO crmPriorities (id, priority, orderBy) VALUES (5,'higher',5);
INSERT INTO crmPriorities (id, priority, orderBy) VALUES (6,'urgent',6);
INSERT INTO crmPriorities (id, priority, orderBy) VALUES (7,'none',7);


DROP TABLE IF EXISTS crmRings;
CREATE TABLE crmRings (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  contactId int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  epoc int(11) DEFAULT NULL,
  answer int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY contactId (contactId,`date`),
  KEY `date` (`date`,contactId)
);




DROP TABLE IF EXISTS crmTags;
CREATE TABLE crmTags (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  img varchar(255) DEFAULT NULL,
  orderBy int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
);


INSERT INTO crmTags (id, name, img, orderBy) VALUES (1,'Busy','grayCircle16x16.png',NULL);
INSERT INTO crmTags (id, name, img, orderBy) VALUES (2,'Wrong Number','thumbDown.png',NULL);
INSERT INTO crmTags (id, name, img, orderBy) VALUES (3,'No Reply','no.png',NULL);
INSERT INTO crmTags (id, name, img, orderBy) VALUES (4,'Call Back','phone.png',NULL);
INSERT INTO crmTags (id, name, img, orderBy) VALUES (5,'Sent Email','email.png',NULL);
INSERT INTO crmTags (id, name, img, orderBy) VALUES (6,'Not Interested','delete.png',NULL);
INSERT INTO crmTags (id, name, img, orderBy) VALUES (7,'Sold','buzzCircle16x16.png',NULL);


DROP TABLE IF EXISTS crmUsers;
CREATE TABLE crmUsers (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  orgId int(11) DEFAULT NULL,
  loginId int(11) DEFAULT NULL,
  mgrId int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  loginEmail varchar(255) DEFAULT NULL,
  passwd varchar(50) DEFAULT NULL,
  `role` varchar(40) DEFAULT NULL,
  lastSeen int(11) DEFAULT NULL,
  lastFelt int(11) DEFAULT NULL,
  lastTouched int(11) DEFAULT NULL,
  lastHealed int(11) DEFAULT NULL,
  createdOn date DEFAULT NULL,
  createdBy varchar(40) DEFAULT NULL,
  lastChange date DEFAULT NULL,
  lastChangeBy varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY loginEmail (loginEmail),
  KEY orgId (orgId),
  KEY orgId_2 (orgId)
);


INSERT INTO crmUsers (id, orgId, loginId, mgrId, name, loginEmail, passwd, role, lastSeen, lastFelt, lastTouched, lastHealed, createdOn, createdBy, lastChange, lastChangeBy) VALUES (2,1,1,1,'ohad aloni','ohad@theora.com','1961','manager',1535511494,1355996797,NULL,1357045858,'2012-12-01',NULL,'2018-08-29','ohad');

