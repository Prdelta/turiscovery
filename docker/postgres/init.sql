-- Base aparte para las pruebas (RefreshDatabase la vacía en cada ejecución)
CREATE DATABASE nanpuno_test OWNER nanpuno;

\connect nanpuno
CREATE EXTENSION IF NOT EXISTS postgis;

\connect nanpuno_test
CREATE EXTENSION IF NOT EXISTS postgis;
