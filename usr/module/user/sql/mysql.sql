# Pi Engine schema
# http://pialog.org
# Author: Taiwen Jiang <taiwenjiang@tsinghua.org.cn>
# --------------------------------------------------------

# ------------------------------------------------------
# User
# >>>>

# user ID: the unique identity in the system
# user identity: the user's unique identity, generated by the system or sent from other systems like openID
# all local data of a user should be indexed by user ID

# User account and authentication data
CREATE TABLE `{account}` (
  `id`              int(10)         unsigned    NOT NULL    auto_increment,
  `identity`        varchar(32)     NOT NULL,
  `credential`      varchar(255)    NOT NULL default '',    # Credential hash
  `salt`            varchar(255)    NOT NULL default '',    # Hash salt
  `email`           varchar(64)     NOT NULL,
  `name`            varchar(255)    NOT NULL default '',
  `active`          tinyint(1)      NOT NULL default '0',

  PRIMARY KEY  (`id`),
  UNIQUE KEY `identity` (`identity`),
  UNIQUE KEY `email` (`email`)
);

# Profile schema for basic fields
CREATE TABLE `{profile}` (
  `id`              int(10)         unsigned    NOT NULL    auto_increment,
  `uid`             int(10)         unsigned    NOT NULL,
  `gender`          varchar(255)    NOT NULL default '',
  `fullname`        varchar(255)    NOT NULL default '',
  `birthdate`       varchar(10)     NOT NULL default '',    # YYYY-mm-dd
  `location`        varchar(255)    NOT NULL default '',
  `signature`       varchar(255)    NOT NULL default '',
  `bio`             text,
  `avatar`          text,           # Link to avatar image, or email for gravatar

  PRIMARY KEY  (`id`),
  UNIQUE KEY `user` (`uid`)
);

# Entity meta for custom user profile fields
CREATE TABLE `{custom}` (
  `id`              int(10)         unsigned    NOT NULL    auto_increment,
  `uid`             int(10)         unsigned    NOT NULL,
  `field`           varchar(64)     NOT NULL,   # Custom field name
  `value`           text,

  PRIMARY KEY  (`id`),
  UNIQUE KEY  `field` (`uid`, `field`)
);

# Entity meta for all profile fields: account, basic profile and custom fields
CREATE TABLE `{field}` (
  `id`              smallint(5)     unsigned    NOT NULL    auto_increment,
  `name`            varchar(64)     NOT NULL,
  `module`          varchar(64)     NOT NULL default '',
  `title`           varchar(255)    NOT NULL default '',
  `edit`            text,           # callback options for edit
  `filter`          text,           # callback options for output filtering

  `type`            ENUM('custom', 'account', 'profile'),   # Field type, default as custom
  `is_edit`         tinyint(1)      NOT NULL default '0',   # Is editable by user
  `is_search`       tinyint(1)      NOT NULL default '0',   # Is searchable
  `is_display`      tinyint(1)      NOT NULL default '0',   # Display on profile page
  `active`          tinyint(1)      NOT NULL default '0',   # Is active

  PRIMARY KEY  (`id`),
  UNIQUE KEY  `name` (`module`, `name`)
);

# Timeline meta
CREATE TABLE `{timeline_type}` (
  `id`              int(10)         unsigned    NOT NULL    auto_increment,
  `name`            varchar(64)     NOT NULL    default '',
  `title`           varchar(255)    NOT NULL    default '',
  `module`          varchar(64)     NOT NULL    default '',
  `icon`            text,

  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`module`, `name`)
);

# Activity meta
CREATE TABLE `{activity}` (
  `id`              int(10)         unsigned    NOT NULL    auto_increment,
  `name`            varchar(64)     NOT NULL    default '',
  `title`           varchar(255)    NOT NULL    default '',
  `module`          varchar(64)     NOT NULL    default '',
  `link`            text,
  `icon`            text,

  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`module`, `name`)
);

# Quicklinks
CREATE TABLE `{quicklink}` (
  `id`              int(10)         unsigned    NOT NULL    auto_increment,
  `name`            varchar(64)     NOT NULL    default '',
  `title`           varchar(255)    NOT NULL    default '',
  `module`          varchar(64)     NOT NULL    default '',
  `link`            text,
  `icon`            text,

  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`module`, `name`)
);

# Timeline for user activities
CREATE TABLE `{timeline}` (
  `id`              int(10)         unsigned    NOT NULL    auto_increment,
  `uid`             int(10)         unsigned    NOT NULL,
  `type`            varchar(64)     NOT NULL    default '',
  `module`          varchar(64)     NOT NULL    default '',
  `message`         text,
  `link`            text,
  `time`            int(11)         unsigned    NOT NULL,

  PRIMARY KEY  (`id`)
);

# ------------------------------------------------------

# user custom contents
CREATE TABLE `{repo}` (
  `id`              int(10)         unsigned    NOT NULL    auto_increment,
  `uid`             int(10)         unsigned    NOT NULL default '0',
  `module`          varchar(64)     NOT NULL    default '',
  `type`            varchar(64)     NOT NULL    default '',
  `content`         text,

  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_content` (`uid`, `module`, `type`)
);

# user-role links for regular
CREATE TABLE `{role}` (
  `id`              int(10)         unsigned    NOT NULL    auto_increment,
  `uid`             int(10)         unsigned    NOT NULL,
  `role`            varchar(64)     NOT NULL    default '',
   `section`        varchar(64)     NOT NULL    default 'front',

  PRIMARY KEY  (`id`),
  UNIQUE KEY `user` (`section`, `uid`)
);
