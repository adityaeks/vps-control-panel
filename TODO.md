# TODO.md

# GLOBAL ROADMAP

---

# PHASE 1 — FOUNDATION

## Setup Project

* [ ] Setup Laravel project
* [ ] Setup TailwindCSS
* [ ] Setup authentication
* [ ] Setup database
* [ ] Setup websocket server
* [ ] Setup Redis

---

# DATABASE DESIGN

## Tables

* [ ] users
* [ ] servers
* [ ] projects
* [ ] deployments
* [ ] terminal_sessions
* [ ] logs
* [ ] server_metrics

---

# VPS MANAGEMENT

## Server CRUD

* [ ] Add VPS
* [ ] Edit VPS
* [ ] Delete VPS
* [ ] Online/offline detection
* [ ] Heartbeat system

---

# AGENT DEVELOPMENT

## Agent Core

* [ ] Create websocket connection
* [ ] Create authentication token
* [ ] Create heartbeat sender
* [ ] Create command runner
* [ ] Create realtime log sender

---

# COMMAND SYSTEM

## Basic Commands

* [ ] Git pull
* [ ] Composer install
* [ ] NPM build
* [ ] Restart PM2
* [ ] Restart Supervisor
* [ ] Restart Docker container

---

# DEPLOYMENT SYSTEM

## Deployment

* [ ] Deploy button
* [ ] Deployment queue
* [ ] Deployment logs
* [ ] Deployment history
* [ ] Deployment status

---

# REALTIME FEATURES

## Websocket

* [ ] Live logs
* [ ] Live terminal
* [ ] Live server metrics
* [ ] Live deployment output

---

# WEB TERMINAL

## Terminal

* [ ] Integrate xterm.js
* [ ] Create PTY backend
* [ ] Create websocket terminal bridge
* [ ] Terminal authentication
* [ ] Terminal session manager

---

# MONITORING

## Metrics

* [ ] CPU monitoring
* [ ] RAM monitoring
* [ ] Disk monitoring
* [ ] Uptime monitoring
* [ ] Network monitoring

---

# DOCKER MANAGEMENT

## Docker

* [ ] List containers
* [ ] Restart container
* [ ] Container logs
* [ ] Docker compose support

---

# SECURITY

## Security Checklist

* [ ] JWT authentication
* [ ] CSRF protection
* [ ] Rate limiting
* [ ] Role permissions
* [ ] Audit logs
* [ ] Encrypted websocket
* [ ] Command validation
* [ ] Restricted shell

---

# UI/UX

## Dashboard

* [ ] Server cards
* [ ] Metrics charts
* [ ] Deployment modal
* [ ] Logs viewer
* [ ] Terminal page

---

# ADVANCED FEATURES

## Future Features

* [ ] Multi-user collaboration
* [ ] CI/CD pipeline
* [ ] GitHub webhook
* [ ] Auto deploy
* [ ] Rollback deployment
* [ ] Notification system
* [ ] Backup system

---

# RELEASE TARGETS

## MVP

Target:

* Multi VPS
* Deploy button
* Logs
* Terminal

## V2

Target:

* Docker support
* Monitoring
* Better security

## V3

Target:

* SaaS ready
* Team collaboration
* Full CI/CD
