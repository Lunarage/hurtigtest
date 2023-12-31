BEGIN;
CREATE SCHEMA IF NOT EXISTS hurtigtest;

SET search_path TO hurtigtest;

CREATE TABLE IF NOT EXISTS tidspunkter (
    id serial,
    start_tid timestamp with time zone NOT NULL,
    slutt_tid timestamp with time zone NOT NULL,
    plasser int NOT NULL,
    CONSTRAINT tidspunkter_pk PRIMARY KEY(id),
    CONSTRAINT unique_timeframe UNIQUE(start_tid, slutt_tid)
  );

REVOKE ALL ON TABLE tidspunkter FROM PUBLIC;
GRANT ALL ON TABLE tidspunkter TO hurtigtest;

CREATE TABLE IF NOT EXISTS paameldinger (
    id serial,
    navn varchar NOT NULL,
    tlf varchar NOT NULL,
    epost varchar NOT NULL,
    foedselsnummer varchar,
    self_declaration bool DEFAULT false NOT NULL,
    paameldingstidspunkt timestamp with time zone DEFAULT NOW(),
    tidspunkt_id int NOT NULL,
    CONSTRAINT paameldinger_pk PRIMARY KEY(id),
    CONSTRAINT paameldinger_unique_tlf_per_tidspunkt UNIQUE(tlf, tidspunkt_id),
    CONSTRAINT tidspunkt_fk FOREIGN KEY(tidspunkt_id) REFERENCES tidspunkter(id)
  );

REVOKE ALL ON TABLE paameldinger FROM PUBLIC;
GRANT ALL ON TABLE paameldinger TO hurtigtest;

GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA hurtigtest TO hurtigtest;

-- Function to check if tidspunkt is fully booked
CREATE OR REPLACE FUNCTION enforce_plasser() RETURNS trigger AS $$
DECLARE 
  antall_paameldinger int;
  antall_plasser int;
  slutt_tid_check timestamp with time zone;
BEGIN
  -- prevent concurrent inserts from multiple transactions
  LOCK TABLE paameldinger IN EXCLUSIVE MODE;

  -- find current number of bookings for a timeframe
  SELECT INTO antall_paameldinger COUNT(*)
  FROM paameldinger
  WHERE tidspunkt_id = NEW.tidspunkt_id;

  -- find maximum number of bookings
  SELECT INTO antall_plasser
  plasser FROM tidspunkter
  WHERE id = NEW.tidspunkt_id;

  -- find end time
  SELECT INTO slutt_tid_check
  slutt_tid FROM tidspunkter
  WHERE id = NEW.tidspunkt_id;

  -- raise exception if the timeframe is fully booked
  IF antall_paameldinger >= antall_plasser THEN
    RAISE EXCEPTION 'Tidspunktet er fullt';
  END IF;

  -- raise exception if the end time is passed
  IF slutt_tid_check < NOW() THEN
    RAISE EXCEPTION 'Tidspunktet er forbipassert';
  END IF;

  -- if all is good, return the row
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- trigger on insert on paameldinger
DROP TRIGGER IF EXISTS enforce_plasser ON paameldinger;
CREATE TRIGGER enforce_plasser
  BEFORE INSERT ON paameldinger
  FOR EACH ROW EXECUTE PROCEDURE enforce_plasser();

END;
