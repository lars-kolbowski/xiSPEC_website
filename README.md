# xiSPEC mass spectrometry visualization tool

Citation: Lars Kolbowski, Colin Combe, Juri Rappsilber; xiSPEC: web-based visualization, analysis and sharing of proteomics data, Nucleic Acids Research, gky353, https://doi.org/10.1093/nar/gky353


### Dependency

parser back-end (https://github.com/Rappsilber-Laboratory/xiSPEC_ms_parser)

linux
apache2
mysql
php

### Note

Annotation of spectra is done per default via xiAnnotator (https://github.com/Rappsilber-Laboratory/xiAnnotator) set up on http://xi3.bio.ed.ac.uk/xiAnnotator/annotate/FULL. Instructions for setting up your own copy of the xiAnnotator can be found here: https://github.com/Rappsilber-Laboratory/xiAnnotator/blob/master/doc/SysV/Readme.md

### Installation

Clone git repository into your web-server directory (e.g. /var/www/html):

```git clone https://github.com/Rappsilber-Laboratory/xiSPEC.git```



MySQL database:

Create database with 3 tables:

```
CREATE TABLE dbs (
 id int(11) NOT NULL AUTO_INCREMENT,
 name varchar(64) DEFAULT NULL,
 email varchar(256) DEFAULT NULL,
 pass text,
 share varchar(64) DEFAULT NULL,
 ip varchar(39) DEFAULT NULL,
 hostname varchar(128) DEFAULT NULL,
 country varchar(64) DEFAULT NULL,
 region varchar(64) DEFAULT NULL,
 city varchar(64) DEFAULT NULL,
 org varchar(128) DEFAULT NULL,
 date datetime DEFAULT NULL,
 size int(11) NOT NULL,
 PRIMARY KEY (id),
 UNIQUE KEY name (name),
 UNIQUE KEY share (share)
);


CREATE TABLE access_log (
 id int(11) NOT NULL AUTO_INCREMENT,
 ip varchar(39) DEFAULT NULL,
 hostname varchar(128) DEFAULT NULL,
 country varchar(64) DEFAULT NULL,
 region varchar(64) DEFAULT NULL,
 city varchar(64) DEFAULT NULL,
 org varchar(64) DEFAULT NULL,
 date date DEFAULT NULL,
 db_id int(11) DEFAULT NULL,
 PRIMARY KEY (id)
);


CREATE TABLE upload_log (
 id int(11) NOT NULL AUTO_INCREMENT,
 id_file varchar(256) NOT NULL,
 pl_file varchar(256) NOT NULL,
 ip varchar(39) NOT NULL,
 hostname varchar(128) DEFAULT NULL,
 country int(64) DEFAULT NULL,
 region int(64) DEFAULT NULL,
 city int(64) DEFAULT NULL,
 org int(128) DEFAULT NULL,
 date datetime NOT NULL,
 PRIMARY KEY (id)
);
```

Open xiSPEC_sql_conn.php.default, 
enter your database credentials into it and save it as xiSPEC_sql_conn.php

Visit http://localhost/xiSPEC/index.php

