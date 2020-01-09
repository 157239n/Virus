create database if not exists virus_app;

create user if not exists virus@localhost identified by 'Vu,-za8M:Yp`D-NR';
grant all privileges on virus_app.* to virus@localhost;
flush privileges;

use virus_app;
CREATE TABLE IF NOT EXISTS users (
    user_handle varchar(20) not null,
    password_hash varchar(64) not null, /* sha256 hash */
    password_salt varchar(5) not null, /* random 5 character string */
    name varchar(100) not null,
    timezone int not null,
    hold bit not null, /* 1 if the installation entry point is blocked off, 0 for freely interchangeable */
    index (user_handle),
    primary key (user_handle)
);

CREATE TABLE IF NOT EXISTS viruses (
    virus_id varchar(64) not null, /* sha256 hash */
    user_handle varchar(20) not null,
    last_ping int not null, /* unix timestamp */
    name varchar(50) not null, /* short text of the virus, equivalent to "target name", if you will */
    active bit not null, /* whether this virus is currently active (pinging back) or not. 0 for not active, 1 for active */
    type bit not null, /* what type is this virus? 0 for normal virus, 1 for swarm */
    class varchar(50) not null, /* like classes in css, this just provides a grouping of attack packages, so viruses know what packages is legal. Each virus can only have 1 class */
    index (virus_id, user_handle),
    primary key (virus_id)
);

CREATE TABLE IF NOT EXISTS attacks (
    attack_id varchar(64) not null, /* sha256 hash */
    virus_id varchar(64) not null, /* sha256 hash */
    attack_package varchar(100) not null, /* attack package, like org.kelvinho.scanPartitions */
    status varchar(10) not null, /* dormant | deployed | executed */
    executed_time int not null default 0, /* unix timestamp */
    name varchar(50) not null, /* short text of the attack, to identify itself */
    index (attack_id, virus_id),
    primary key (attack_id)
);

CREATE TABLE IF NOT EXISTS uptimes ( /* used to make virus uptime graph */
	virus_id varchar(64) not null,
	unix_time int not null,
	active bit not null,
	index (virus_id, unix_time),
	primary key (virus_id, unix_time)
);
