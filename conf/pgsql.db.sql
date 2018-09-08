--
-- PostgreSQL database dump
--

-- Dumped from database version 9.5.1
-- Dumped by pg_dump version 9.5.1

-- Started on 2018-09-08 07:56:19

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 1 (class 3079 OID 12355)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 2164 (class 0 OID 0)
-- Dependencies: 1
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- TOC entry 203 (class 1255 OID 16632)
-- Name: has_folder_permission(integer, integer); Type: FUNCTION; Schema: public; Owner: -
--

CREATE FUNCTION has_folder_permission(_folder_id integer, _user_id integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
	result BOOLEAN := FALSE;

BEGIN

	LOOP
		result := (SELECT COUNT(*) > 0 FROM ((SELECT TRUE FROM folders WHERE folder_id = _folder_id AND (owner_id = _user_id OR owner_id IS NULL)) UNION (SELECT TRUE FROM folder_roles, role_users WHERE folder_roles.folder_id = _folder_id AND folder_roles.role_id = role_users.role_id AND role_users.user_id = _user_id)) A);
		_folder_id := (SELECT parent_folder_id FROM folders WHERE folder_id = _folder_id);
		EXIT WHEN NOT result OR _folder_id IS NULL;
	END LOOP;

	RETURN result;
END;
$$;


SET default_with_oids = false;

--
-- TOC entry 181 (class 1259 OID 16395)
-- Name: files; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE files (
    file_id integer NOT NULL,
    file_name text NOT NULL,
    file_mime_type text NOT NULL,
    file_description text,
    file_data bytea NOT NULL,
    file_date timestamp with time zone NOT NULL,
    folder_id integer NOT NULL
);


--
-- TOC entry 182 (class 1259 OID 16401)
-- Name: files_file_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE files_file_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2165 (class 0 OID 0)
-- Dependencies: 182
-- Name: files_file_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE files_file_id_seq OWNED BY files.file_id;


--
-- TOC entry 183 (class 1259 OID 16403)
-- Name: folder_roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE folder_roles (
    folder_id integer NOT NULL,
    role_id integer NOT NULL
);


--
-- TOC entry 184 (class 1259 OID 16406)
-- Name: folders; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE folders (
    folder_id integer NOT NULL,
    folder_name text NOT NULL,
    parent_folder_id integer,
    owner_id integer
);


--
-- TOC entry 185 (class 1259 OID 16412)
-- Name: folders_folder_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE folders_folder_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2166 (class 0 OID 0)
-- Dependencies: 185
-- Name: folders_folder_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE folders_folder_id_seq OWNED BY folders.folder_id;


--
-- TOC entry 186 (class 1259 OID 16414)
-- Name: role_users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE role_users (
    role_id integer NOT NULL,
    user_id integer NOT NULL
);


--
-- TOC entry 187 (class 1259 OID 16417)
-- Name: roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE roles (
    role_id integer NOT NULL,
    role_name text NOT NULL
);


--
-- TOC entry 188 (class 1259 OID 16423)
-- Name: roles_role_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE roles_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2167 (class 0 OID 0)
-- Dependencies: 188
-- Name: roles_role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE roles_role_id_seq OWNED BY roles.role_id;


--
-- TOC entry 189 (class 1259 OID 16425)
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE users (
    user_id integer NOT NULL,
    username text NOT NULL,
    password text NOT NULL
);


--
-- TOC entry 190 (class 1259 OID 16431)
-- Name: users_user_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE users_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2168 (class 0 OID 0)
-- Dependencies: 190
-- Name: users_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE users_user_id_seq OWNED BY users.user_id;


--
-- TOC entry 2012 (class 2604 OID 16433)
-- Name: file_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY files ALTER COLUMN file_id SET DEFAULT nextval('files_file_id_seq'::regclass);


--
-- TOC entry 2013 (class 2604 OID 16434)
-- Name: folder_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY folders ALTER COLUMN folder_id SET DEFAULT nextval('folders_folder_id_seq'::regclass);


--
-- TOC entry 2014 (class 2604 OID 16435)
-- Name: role_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY roles ALTER COLUMN role_id SET DEFAULT nextval('roles_role_id_seq'::regclass);


--
-- TOC entry 2015 (class 2604 OID 16436)
-- Name: user_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY users ALTER COLUMN user_id SET DEFAULT nextval('users_user_id_seq'::regclass);


--
-- TOC entry 2017 (class 2606 OID 16440)
-- Name: files_file_name_folder_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY files
    ADD CONSTRAINT files_file_name_folder_id_key UNIQUE (file_name, folder_id);


--
-- TOC entry 2019 (class 2606 OID 16442)
-- Name: files_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY files
    ADD CONSTRAINT files_pkey PRIMARY KEY (file_id);


--
-- TOC entry 2021 (class 2606 OID 16444)
-- Name: folder_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY folder_roles
    ADD CONSTRAINT folder_roles_pkey PRIMARY KEY (folder_id, role_id);


--
-- TOC entry 2024 (class 2606 OID 16514)
-- Name: folders_folder_name_parent_folder_id_owner_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY folders
    ADD CONSTRAINT folders_folder_name_parent_folder_id_owner_id_key UNIQUE (folder_name, parent_folder_id, owner_id);


--
-- TOC entry 2026 (class 2606 OID 16448)
-- Name: folders_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY folders
    ADD CONSTRAINT folders_pkey PRIMARY KEY (folder_id);


--
-- TOC entry 2028 (class 2606 OID 16450)
-- Name: role_users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY role_users
    ADD CONSTRAINT role_users_pkey PRIMARY KEY (role_id, user_id);


--
-- TOC entry 2030 (class 2606 OID 16452)
-- Name: roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (role_id);


--
-- TOC entry 2032 (class 2606 OID 16454)
-- Name: roles_role_name_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY roles
    ADD CONSTRAINT roles_role_name_key UNIQUE (role_name);


--
-- TOC entry 2034 (class 2606 OID 16456)
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (user_id);


--
-- TOC entry 2036 (class 2606 OID 16458)
-- Name: users_username_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- TOC entry 2022 (class 1259 OID 16512)
-- Name: fki_folders_owner_id_fkey; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX fki_folders_owner_id_fkey ON folders USING btree (owner_id);


--
-- TOC entry 2037 (class 2606 OID 16459)
-- Name: files_folder_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY files
    ADD CONSTRAINT files_folder_id_fkey FOREIGN KEY (folder_id) REFERENCES folders(folder_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2038 (class 2606 OID 16464)
-- Name: folder_roles_folder_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY folder_roles
    ADD CONSTRAINT folder_roles_folder_id_fkey FOREIGN KEY (folder_id) REFERENCES folders(folder_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2039 (class 2606 OID 16469)
-- Name: folder_roles_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY folder_roles
    ADD CONSTRAINT folder_roles_role_id_fkey FOREIGN KEY (role_id) REFERENCES roles(role_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2040 (class 2606 OID 16474)
-- Name: folders_parent_folder_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY folders
    ADD CONSTRAINT folders_parent_folder_id_fkey FOREIGN KEY (parent_folder_id) REFERENCES folders(folder_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2041 (class 2606 OID 16515)
-- Name: foldes_owner_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY folders
    ADD CONSTRAINT foldes_owner_id_fkey FOREIGN KEY (owner_id) REFERENCES users(user_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2042 (class 2606 OID 16479)
-- Name: role_users_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY role_users
    ADD CONSTRAINT role_users_role_id_fkey FOREIGN KEY (role_id) REFERENCES roles(role_id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2043 (class 2606 OID 16484)
-- Name: role_users_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY role_users
    ADD CONSTRAINT role_users_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(user_id) ON UPDATE CASCADE ON DELETE RESTRICT;


-- Completed on 2018-09-08 07:56:19

--
-- PostgreSQL database dump complete
--

