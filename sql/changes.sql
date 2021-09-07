ALTER TABLE tidspunkter ADD CONSTRAINT unique_timeframe UNIQUE(start_tid, slutt_tid);
