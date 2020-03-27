CREATE TABLE if not exists `typecho_unitypecho` (
  `id`                INTEGER NOT NULL PRIMARY KEY,
  `openid`            varchar(255)     default ''  ,
  `createtime`        int(10)          default 0   ,
  `lastlogin`         int(10)          default 0   ,
  `nickname`          varchar(255)     default ''  ,
  `avatarUrl`         varchar(255)     default ''  ,
  `city`              varchar(255)     default ''  ,
  `country`           varchar(255)     default ''  ,
  `gender`            varchar(255)     default ''  ,
  `province`          varchar(255)     default ''  ,
  `mp`                varchar(255)     default ''  ,
  `formid`          varchar(255)     default ''
);
CREATE TABLE if not exists `typecho_unitypecholike` (
  `id`                INTEGER NOT NULL PRIMARY KEY,
  `openid`            varchar(255)     default ''  ,
  `cid`               int(10)          default 0
);
