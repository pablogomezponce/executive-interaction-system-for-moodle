create schema PWPOP collate utf8_general_ci;

create table Favorites
(
    user varchar(255) not null,
    product int not null,
    primary key (user, product)
)
    collate=utf8mb4_unicode_ci;

create table Product
(
    title mediumtext null,
    description mediumtext null,
    price float null,
    product_image_dir mediumtext null,
    category mediumtext null,
    id bigint unsigned auto_increment,
    isActive tinyint(1) default 1 null,
    constraint id
        unique (id)
)
    collate=utf8mb4_unicode_ci;

alter table Product
    add primary key (id);

create table User
(
    username varchar(255) null,
    email varchar(255) not null
        primary key,
    password varchar(255) null,
    name varchar(255) null,
    birthdate date null,
    phone bigint null,
    image_dir mediumtext null,
    isActive tinyint(1) default 1 null,
    id bigint unsigned auto_increment,
    constraint id
        unique (id)
)
    collate=utf8mb4_unicode_ci;

create table UserProductBuy
(
    buyer varchar(255) not null,
    product int not null,
    primary key (buyer, product)
)
    collate=utf8mb4_unicode_ci;

create table UserProductOwn
(
    owner varchar(255) not null,
    product int not null,
    buyed tinyint(1) null,
    primary key (owner, product)
)
    collate=utf8mb4_unicode_ci;