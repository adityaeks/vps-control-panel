# README.md

# VPS Control Panel

Modern self-hosted VPS management dashboard with:

* Multi VPS support
* Realtime web terminal
* Git deployment
* Logs monitoring
* Docker management
* Realtime websocket communication
* Deployment automation

---

# Features

## Core Features

* Multi VPS management
* Server monitoring
* Realtime server status
* Project management
* Git pull deployment
* Service restart
* Realtime logs
* Web terminal (CLI)

---

# Planned Features

## Deployment

* GitHub integration
* Branch selection
* Auto deploy
* Deployment history
* Rollback system

## Monitoring

* CPU usage
* RAM usage
* Disk usage
* Network traffic
* Realtime charts

## Docker

* List containers
* Restart container
* Container logs
* Docker compose support

## Security

* JWT authentication
* RBAC permissions
* Audit logs
* 2FA
* Encrypted websocket
* Command whitelist

---

# Architecture

Browser
↓
Main Dashboard
↓
WebSocket + API
↓
Agent VPS
↓
Linux Shell / Docker / Git

---

# Tech Stack

## Frontend

* Laravel Blade
* TailwindCSS
* AlpineJS
* xterm.js

## Backend

* Laravel
* Redis
* MySQL

## Realtime

* Laravel Reverb
  or
* Node.js WebSocket

## VPS Agent

Recommended:

* Go

Alternative:

* Node.js

---

# Project Structure

/backend
/frontend
/agent
/docker
/docs

---

# Main Modules

## Dashboard

Handle:

* authentication
* server management
* project management
* deployment history

## Agent

Handle:

* command execution
* terminal session
* metrics collection
* websocket communication

## Realtime Engine

Handle:

* live logs
* terminal stream
* server status updates

---

# Initial MVP

## Phase 1

* Authentication
* Add VPS
* Server status
* Execute predefined commands

## Phase 2

* Git deploy
* Restart service
* Realtime logs

## Phase 3

* Web terminal
* Realtime command execution

## Phase 4

* Docker management

---

# Security Notes

NEVER:

* expose root shell
* save root password
* allow unrestricted command execution

ALWAYS:

* use restricted linux user
* use token authentication
* log all command activity
* validate commands
* use HTTPS/WSS

---

# Future Vision

This project aims to become:

* self-hosted deployment platform
* modern VPS control panel
* lightweight DevOps automation tool

Inspired by:

* Coolify
* Portainer
* Pterodactyl
* Webmin

---

# Development Status

Current Status:
Planning & architecture stage.
