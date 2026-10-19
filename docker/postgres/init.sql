-- Base aparte para las pruebas (RefreshDatabase la vacía en cada ejecución)
CREATE DATABASE turiscovery_test OWNER turiscovery;

\connect turiscovery
CREATE EXTENSION IF NOT EXISTS postgis;

\connect turiscovery_test
CREATE EXTENSION IF NOT EXISTS postgis;
