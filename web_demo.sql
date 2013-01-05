--
-- PostgreSQL database dump
--

-- Dumped from database version 9.1.7
-- Dumped by pg_dump version 9.1.7
-- Started on 2013-01-04 11:52:20 PST

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 171 (class 3079 OID 11676)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 1959 (class 0 OID 0)
-- Dependencies: 171
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 165 (class 1259 OID 16644)
-- Dependencies: 1914 6
-- Name: alarm_entry; Type: TABLE; Schema: public; Owner: codeigniter; Tablespace: 
--

CREATE TABLE alarm_entry (
    device_id character varying(64) NOT NULL,
    frame_id integer NOT NULL,
    set_ts timestamp without time zone NOT NULL,
    reset_ts timestamp without time zone DEFAULT '1900-01-01 00:00:00'::timestamp without time zone,
    can_value integer,
    id integer NOT NULL
);


ALTER TABLE public.alarm_entry OWNER TO codeigniter;

--
-- TOC entry 1960 (class 0 OID 0)
-- Dependencies: 165
-- Name: COLUMN alarm_entry.id; Type: COMMENT; Schema: public; Owner: codeigniter
--

COMMENT ON COLUMN alarm_entry.id IS 'Primary key id';


--
-- TOC entry 169 (class 1259 OID 16698)
-- Dependencies: 165 6
-- Name: alarm_entry_id_seq; Type: SEQUENCE; Schema: public; Owner: codeigniter
--

CREATE SEQUENCE alarm_entry_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.alarm_entry_id_seq OWNER TO codeigniter;

--
-- TOC entry 1961 (class 0 OID 0)
-- Dependencies: 169
-- Name: alarm_entry_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: codeigniter
--

ALTER SEQUENCE alarm_entry_id_seq OWNED BY alarm_entry.id;


--
-- TOC entry 163 (class 1259 OID 16613)
-- Dependencies: 6
-- Name: alarm_specification; Type: TABLE; Schema: public; Owner: codeigniter; Tablespace: 
--

CREATE TABLE alarm_specification (
    frame_id integer NOT NULL,
    device_id character varying(64) NOT NULL,
    trigger_threshold integer NOT NULL,
    reset_threshold integer NOT NULL
);


ALTER TABLE public.alarm_specification OWNER TO codeigniter;

--
-- TOC entry 161 (class 1259 OID 16603)
-- Dependencies: 6
-- Name: can_frame; Type: TABLE; Schema: public; Owner: codeigniter; Tablespace: 
--

CREATE TABLE can_frame (
    frame_id integer NOT NULL,
    label character varying(64),
    description character varying(255),
    unit_of_measurement character varying(64),
    min_value integer,
    max_value integer
);


ALTER TABLE public.can_frame OWNER TO codeigniter;

--
-- TOC entry 162 (class 1259 OID 16608)
-- Dependencies: 6
-- Name: device; Type: TABLE; Schema: public; Owner: codeigniter; Tablespace: 
--

CREATE TABLE device (
    device_id character varying(64) NOT NULL,
    can_speed integer NOT NULL,
    can_frame_type integer NOT NULL,
    connect_retry_count integer NOT NULL,
    connect_retry_interval integer NOT NULL,
    waypoint_interval real,
    description character varying(255),
    device_key character varying(64) NOT NULL,
    server_key character varying(64) NOT NULL
);


ALTER TABLE public.device OWNER TO codeigniter;

--
-- TOC entry 1962 (class 0 OID 0)
-- Dependencies: 162
-- Name: COLUMN device.description; Type: COMMENT; Schema: public; Owner: codeigniter
--

COMMENT ON COLUMN device.description IS 'Textual description of device';


--
-- TOC entry 1963 (class 0 OID 0)
-- Dependencies: 162
-- Name: COLUMN device.device_key; Type: COMMENT; Schema: public; Owner: codeigniter
--

COMMENT ON COLUMN device.device_key IS 'Device key';


--
-- TOC entry 1964 (class 0 OID 0)
-- Dependencies: 162
-- Name: COLUMN device.server_key; Type: COMMENT; Schema: public; Owner: codeigniter
--

COMMENT ON COLUMN device.server_key IS 'Server key';


--
-- TOC entry 164 (class 1259 OID 16628)
-- Dependencies: 6
-- Name: log_entry; Type: TABLE; Schema: public; Owner: codeigniter; Tablespace: 
--

CREATE TABLE log_entry (
    device_id character varying(64) NOT NULL,
    frame_id integer NOT NULL,
    ts timestamp without time zone,
    can_value integer,
    id integer NOT NULL
);


ALTER TABLE public.log_entry OWNER TO codeigniter;

--
-- TOC entry 170 (class 1259 OID 16746)
-- Dependencies: 164 6
-- Name: log_entry_id_seq; Type: SEQUENCE; Schema: public; Owner: codeigniter
--

CREATE SEQUENCE log_entry_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.log_entry_id_seq OWNER TO codeigniter;

--
-- TOC entry 1965 (class 0 OID 0)
-- Dependencies: 170
-- Name: log_entry_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: codeigniter
--

ALTER SEQUENCE log_entry_id_seq OWNED BY log_entry.id;


--
-- TOC entry 167 (class 1259 OID 16667)
-- Dependencies: 6
-- Name: log_specification; Type: TABLE; Schema: public; Owner: codeigniter; Tablespace: 
--

CREATE TABLE log_specification (
    frame_id integer NOT NULL,
    sample_interval integer NOT NULL,
    buffer_size integer NOT NULL,
    device_id character varying(64) NOT NULL
);


ALTER TABLE public.log_specification OWNER TO codeigniter;

--
-- TOC entry 166 (class 1259 OID 16651)
-- Dependencies: 6
-- Name: waypoint_entry; Type: TABLE; Schema: public; Owner: codeigniter; Tablespace: 
--

CREATE TABLE waypoint_entry (
    device_id character varying(64) NOT NULL,
    ts timestamp without time zone,
    lat real NOT NULL,
    lon real NOT NULL,
    id integer NOT NULL
);


ALTER TABLE public.waypoint_entry OWNER TO codeigniter;

--
-- TOC entry 1966 (class 0 OID 0)
-- Dependencies: 166
-- Name: COLUMN waypoint_entry.id; Type: COMMENT; Schema: public; Owner: codeigniter
--

COMMENT ON COLUMN waypoint_entry.id IS 'Unique ID';


--
-- TOC entry 168 (class 1259 OID 16689)
-- Dependencies: 6 166
-- Name: waypoint_entry_id_seq; Type: SEQUENCE; Schema: public; Owner: codeigniter
--

CREATE SEQUENCE waypoint_entry_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.waypoint_entry_id_seq OWNER TO codeigniter;

--
-- TOC entry 1967 (class 0 OID 0)
-- Dependencies: 168
-- Name: waypoint_entry_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: codeigniter
--

ALTER SEQUENCE waypoint_entry_id_seq OWNED BY waypoint_entry.id;


--
-- TOC entry 1913 (class 2604 OID 16700)
-- Dependencies: 169 165
-- Name: id; Type: DEFAULT; Schema: public; Owner: codeigniter
--

ALTER TABLE ONLY alarm_entry ALTER COLUMN id SET DEFAULT nextval('alarm_entry_id_seq'::regclass);


--
-- TOC entry 1912 (class 2604 OID 16748)
-- Dependencies: 170 164
-- Name: id; Type: DEFAULT; Schema: public; Owner: codeigniter
--

ALTER TABLE ONLY log_entry ALTER COLUMN id SET DEFAULT nextval('log_entry_id_seq'::regclass);


--
-- TOC entry 1915 (class 2604 OID 16691)
-- Dependencies: 168 166
-- Name: id; Type: DEFAULT; Schema: public; Owner: codeigniter
--

ALTER TABLE ONLY waypoint_entry ALTER COLUMN id SET DEFAULT nextval('waypoint_entry_id_seq'::regclass);


--
-- TOC entry 1946 (class 0 OID 16644)
-- Dependencies: 165 1952
-- Data for Name: alarm_entry; Type: TABLE DATA; Schema: public; Owner: codeigniter
--

COPY alarm_entry (device_id, frame_id, set_ts, reset_ts, can_value, id) FROM stdin;
1	1000	2013-01-03 15:28:54.612928	2013-01-03 16:00:14	23	3
1	1000	2013-01-03 16:00:30.457	2013-01-03 16:01:01	23	4
1	1001	2013-01-03 16:00:55.392	2013-01-03 16:01:02	47	5
1	1001	2013-01-03 16:05:09.413	2013-01-03 16:05:35	47	6
1	1001	2013-01-03 16:05:13.301	2013-01-03 16:05:36	48	7
1	1001	2013-01-03 16:05:15.45	2013-01-03 16:05:37	49	8
\.


--
-- TOC entry 1968 (class 0 OID 0)
-- Dependencies: 169
-- Name: alarm_entry_id_seq; Type: SEQUENCE SET; Schema: public; Owner: codeigniter
--

SELECT pg_catalog.setval('alarm_entry_id_seq', 8, true);


--
-- TOC entry 1944 (class 0 OID 16613)
-- Dependencies: 163 1952
-- Data for Name: alarm_specification; Type: TABLE DATA; Schema: public; Owner: codeigniter
--

COPY alarm_specification (frame_id, device_id, trigger_threshold, reset_threshold) FROM stdin;
\.


--
-- TOC entry 1942 (class 0 OID 16603)
-- Dependencies: 161 1952
-- Data for Name: can_frame; Type: TABLE DATA; Schema: public; Owner: codeigniter
--

COPY can_frame (frame_id, label, description, unit_of_measurement, min_value, max_value) FROM stdin;
1000	ignition	Ignition state\noff forward reverse	enum	0	2
1001	speed	Speed of vehicle	kph	0	255
\.


--
-- TOC entry 1943 (class 0 OID 16608)
-- Dependencies: 162 1952
-- Data for Name: device; Type: TABLE DATA; Schema: public; Owner: codeigniter
--

COPY device (device_id, can_speed, can_frame_type, connect_retry_count, connect_retry_interval, waypoint_interval, description, device_key, server_key) FROM stdin;
123123	250	11	1	1	1	Device number 12	2	1
\.


--
-- TOC entry 1945 (class 0 OID 16628)
-- Dependencies: 164 1952
-- Data for Name: log_entry; Type: TABLE DATA; Schema: public; Owner: codeigniter
--

COPY log_entry (device_id, frame_id, ts, can_value, id) FROM stdin;
\.


--
-- TOC entry 1969 (class 0 OID 0)
-- Dependencies: 170
-- Name: log_entry_id_seq; Type: SEQUENCE SET; Schema: public; Owner: codeigniter
--

SELECT pg_catalog.setval('log_entry_id_seq', 5435, true);


--
-- TOC entry 1948 (class 0 OID 16667)
-- Dependencies: 167 1952
-- Data for Name: log_specification; Type: TABLE DATA; Schema: public; Owner: codeigniter
--

COPY log_specification (frame_id, sample_interval, buffer_size, device_id) FROM stdin;
\.


--
-- TOC entry 1947 (class 0 OID 16651)
-- Dependencies: 166 1952
-- Data for Name: waypoint_entry; Type: TABLE DATA; Schema: public; Owner: codeigniter
--

COPY waypoint_entry (device_id, ts, lat, lon, id) FROM stdin;
\.


--
-- TOC entry 1970 (class 0 OID 0)
-- Dependencies: 168
-- Name: waypoint_entry_id_seq; Type: SEQUENCE SET; Schema: public; Owner: codeigniter
--

SELECT pg_catalog.setval('waypoint_entry_id_seq', 7951, true);


--
-- TOC entry 1927 (class 2606 OID 16707)
-- Dependencies: 165 165 1953
-- Name: alarm_entry_pk; Type: CONSTRAINT; Schema: public; Owner: codeigniter; Tablespace: 
--

ALTER TABLE ONLY alarm_entry
    ADD CONSTRAINT alarm_entry_pk PRIMARY KEY (id);


--
-- TOC entry 1921 (class 2606 OID 16688)
-- Dependencies: 163 163 163 1953
-- Name: alarm_specification_pk; Type: CONSTRAINT; Schema: public; Owner: codeigniter; Tablespace: 
--

ALTER TABLE ONLY alarm_specification
    ADD CONSTRAINT alarm_specification_pk PRIMARY KEY (device_id, frame_id);


--
-- TOC entry 1919 (class 2606 OID 16612)
-- Dependencies: 162 162 1953
-- Name: device_id_key; Type: CONSTRAINT; Schema: public; Owner: codeigniter; Tablespace: 
--

ALTER TABLE ONLY device
    ADD CONSTRAINT device_id_key PRIMARY KEY (device_id);


--
-- TOC entry 1917 (class 2606 OID 16607)
-- Dependencies: 161 161 1953
-- Name: frame_id_pk; Type: CONSTRAINT; Schema: public; Owner: codeigniter; Tablespace: 
--

ALTER TABLE ONLY can_frame
    ADD CONSTRAINT frame_id_pk PRIMARY KEY (frame_id);


--
-- TOC entry 1924 (class 2606 OID 16755)
-- Dependencies: 164 164 1953
-- Name: log_entry_pk; Type: CONSTRAINT; Schema: public; Owner: codeigniter; Tablespace: 
--

ALTER TABLE ONLY log_entry
    ADD CONSTRAINT log_entry_pk PRIMARY KEY (id);


--
-- TOC entry 1934 (class 2606 OID 16686)
-- Dependencies: 167 167 167 1953
-- Name: log_specification_pk; Type: CONSTRAINT; Schema: public; Owner: codeigniter; Tablespace: 
--

ALTER TABLE ONLY log_specification
    ADD CONSTRAINT log_specification_pk PRIMARY KEY (device_id, frame_id);


--
-- TOC entry 1931 (class 2606 OID 16736)
-- Dependencies: 166 166 1953
-- Name: waypoint_entry_pk; Type: CONSTRAINT; Schema: public; Owner: codeigniter; Tablespace: 
--

ALTER TABLE ONLY waypoint_entry
    ADD CONSTRAINT waypoint_entry_pk PRIMARY KEY (id);


--
-- TOC entry 1925 (class 1259 OID 16649)
-- Dependencies: 165 165 165 1953
-- Name: alarm_entry_composite_index; Type: INDEX; Schema: public; Owner: codeigniter; Tablespace: 
--

CREATE INDEX alarm_entry_composite_index ON alarm_entry USING btree (device_id, frame_id, set_ts);


--
-- TOC entry 1928 (class 1259 OID 16650)
-- Dependencies: 165 1953
-- Name: alarm_entry_reset_index; Type: INDEX; Schema: public; Owner: codeigniter; Tablespace: 
--

CREATE INDEX alarm_entry_reset_index ON alarm_entry USING btree (reset_ts);


--
-- TOC entry 1922 (class 1259 OID 16633)
-- Dependencies: 164 164 164 1953
-- Name: log_entry_composite_index; Type: INDEX; Schema: public; Owner: codeigniter; Tablespace: 
--

CREATE INDEX log_entry_composite_index ON log_entry USING btree (device_id, frame_id, ts);


--
-- TOC entry 1932 (class 1259 OID 16682)
-- Dependencies: 167 167 1953
-- Name: log_specification_index; Type: INDEX; Schema: public; Owner: codeigniter; Tablespace: 
--

CREATE UNIQUE INDEX log_specification_index ON log_specification USING btree (device_id, frame_id);


--
-- TOC entry 1929 (class 1259 OID 16697)
-- Dependencies: 166 1953
-- Name: waypoint_entry_device_id_idx; Type: INDEX; Schema: public; Owner: codeigniter; Tablespace: 
--

CREATE INDEX waypoint_entry_device_id_idx ON waypoint_entry USING btree (device_id);


--
-- TOC entry 1935 (class 2606 OID 16618)
-- Dependencies: 163 1916 161 1953
-- Name: alarm_specification_can_frame_fk; Type: FK CONSTRAINT; Schema: public; Owner: codeigniter
--

ALTER TABLE ONLY alarm_specification
    ADD CONSTRAINT alarm_specification_can_frame_fk FOREIGN KEY (frame_id) REFERENCES can_frame(frame_id);


--
-- TOC entry 1936 (class 2606 OID 16623)
-- Dependencies: 1918 163 162 1953
-- Name: alarm_specification_device_id_fl; Type: FK CONSTRAINT; Schema: public; Owner: codeigniter
--

ALTER TABLE ONLY alarm_specification
    ADD CONSTRAINT alarm_specification_device_id_fl FOREIGN KEY (device_id) REFERENCES device(device_id);


--
-- TOC entry 1937 (class 2606 OID 16634)
-- Dependencies: 164 161 1916 1953
-- Name: log_entry_can_frame_fk; Type: FK CONSTRAINT; Schema: public; Owner: codeigniter
--

ALTER TABLE ONLY log_entry
    ADD CONSTRAINT log_entry_can_frame_fk FOREIGN KEY (frame_id) REFERENCES can_frame(frame_id);


--
-- TOC entry 1938 (class 2606 OID 16639)
-- Dependencies: 1918 164 162 1953
-- Name: log_entry_device_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: codeigniter
--

ALTER TABLE ONLY log_entry
    ADD CONSTRAINT log_entry_device_id_fk FOREIGN KEY (device_id) REFERENCES device(device_id);


--
-- TOC entry 1940 (class 2606 OID 16672)
-- Dependencies: 1916 167 161 1953
-- Name: log_specification_can_frame_fk; Type: FK CONSTRAINT; Schema: public; Owner: codeigniter
--

ALTER TABLE ONLY log_specification
    ADD CONSTRAINT log_specification_can_frame_fk FOREIGN KEY (frame_id) REFERENCES can_frame(frame_id);


--
-- TOC entry 1941 (class 2606 OID 16677)
-- Dependencies: 167 1918 162 1953
-- Name: log_specification_device_id_fl; Type: FK CONSTRAINT; Schema: public; Owner: codeigniter
--

ALTER TABLE ONLY log_specification
    ADD CONSTRAINT log_specification_device_id_fl FOREIGN KEY (device_id) REFERENCES device(device_id);


--
-- TOC entry 1939 (class 2606 OID 16662)
-- Dependencies: 162 166 1918 1953
-- Name: waypoint_entry_device_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: codeigniter
--

ALTER TABLE ONLY waypoint_entry
    ADD CONSTRAINT waypoint_entry_device_id_fk FOREIGN KEY (device_id) REFERENCES device(device_id);


--
-- TOC entry 1958 (class 0 OID 0)
-- Dependencies: 6
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2013-01-04 11:52:21 PST

--
-- PostgreSQL database dump complete
--

