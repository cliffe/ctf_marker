# CTF Website

# Using the makefile

```
make fresh-start
```
Fresh start will spawn the website + mysql. You can look at it on http://localhost:8080/


When developing run:

```
make reload
```

This will stop, remove the old application, build the new one and make it run on port 8080

```
make clean
```

This will remove everything and clean up when you have finished.

# Running in docker!!!! Its a new magical world

docker build -t braderz/ctf-website .

docker network create ctf-network

docker run --net ctf-network --name ctf-mysql -p 3306:3306 -e MYSQL_ROOT_PASSWORD=my-secret-pw -e MYSQL_DATABASE=ctff -d mysql:5.7.19

docker run --net ctf-network --name ctf-website -p 8080:80 -d braderz/ctf-website



