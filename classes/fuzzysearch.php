<?
        class FuzzySearch
        {


                /**
                * returns the levenshtein distance between two strings as a percentage of the
length of the first
                * string
                */
                function calculateLevenshteinPercentage($str1,$str2)
                {
                        $dist = levenshtein($str1,$str2);
                        $diff = strlen($str1)-$dist;

                        if($diff>0)
                        {
                                $ret = ($dist*100)/strlen($str1);
                        }

                        else
                        {
                                $ret = 100;
                        }

                        return $ret;
                }

                function singleWordMatches($match,$table,$field)
                {
                        $db = new database();
			$db->query("select $field from ".$table);
                        $ret = array();

                        while($row = $db->getNextRow())
                        {
                                $db_string = $row[$field];
                                $db_phone = metaphone($db_string);
                                $match_phone = metaphone($match);
                                $db_soundex = soundex($db_string);
                                $match_soundex = soundex($match);
                                $perc = FuzzySearch::calculateLevenshteinPercentage($match,$db_string);
                                $phone_perc = FuzzySearch::calculateLevenshteinPercentage($db_phone,$match_phone);
                                $sound_perc =
FuzzySearch::calculateLevenshteinPercentage($db_soundex,$match_soundex);

//echo("match: ".$match."<br>".
//         "dbstring: ".$db_string."<br>".
//         "perc: ".$perc."<br>".
//         "phone_perc: ".$phone_perc."<br>".
//         "sound_perc: ".$sound_perc."<p>")

                                if(($perc<70)&&
                                   ($phone_perc<70)&&
                                   ($sound_perc<70))
                                {
					$details['value'] = $row[$field];
					$details['perc'] = $perc;
					$details['phone_perc'] = $phone_perc;
					$details['sound_perc'] = $sound_perc;
					$details['avg_perc'] = ($perc + $phone_perc + $sound_perc) / 3;
//					print "word=$match\n";
//					print_r($details);
                                        if($details['avg_perc'] < 40) {
//						print "possible match\n";
						$ret[$row[$field]] = $details;
					}
                                }
                        }

                        return $ret;
                }
        }
?>
