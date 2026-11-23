Select *
from tipico_bet b
         join tipico_both_teams_score_odd o on o.bet_id = b.id
where b.start_at_time_stamp > 1711922400000;