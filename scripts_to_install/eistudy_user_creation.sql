CREATE user 'eistudyuser'@'%' IDENTIFIED BY # 'password';

GRANT SELECT ON eistudy.* TO eistudyuser@'%';