<?php

header('Content-Type: application/json');
require('../www/core/core.php');
ini_set("allow_url_fopen", true);
error_reporting(-1);

function validate_M1DEVID($devid, $devsecret) {
    $mysqli = DB_CONNECT();
    $query = $mysqli->query("SELECT * FROM M1_devices WHERE devid='$devid' AND devsecret='$devsecret' AND status='1' LIMIT 1;") or die('false');
    if ($query->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function userfromDevId($devid) {
    $mysqli = DB_CONNECT();
    $query = $mysqli->query("SELECT * FROM M1_devices WHERE devid='$devid';") or die('false');
    if ($query->num_rows > 0) {
        $row = $query->fetch_assoc();
        return $row['username'];
    } else {
        return false;
    }
}
function userfromEmail($email) {
    $mysqli = DB_CONNECT();
    $query = $mysqli->query("SELECT ID,user_login FROM `M1_users` WHERE user_email='".$mysqli->real_escape_string($email)."' LIMIT 1;") or die('false');
    if ($query->num_rows > 0) {
        $row = $query->fetch_assoc();
        return $row['user_login'];
    } else {
        return false;
    }
}

if (isset($_GET['a'])) {

    switch (REPLACE_ALPHANUM($_GET['a'])) {
        case 'login': {
                if (isset($_GET['username']) && isset($_GET['password'])) {
                    if (strlen($_GET['username']) > 2 && strlen($_GET['password']) > 2) {
                        require('wp-load.php');
                        require('wp-config.php');
                        $mysqli = DB_CONNECT();
                        $username = $_GET['username'];
                        $password = urldecode($_GET['password']);
                        $creds = array();
                        if (!filter_var($username, FILTER_VALIDATE_EMAIL) === false) {
                            $username = userfromEmail($mysqli->real_escape_string($_GET['username']));
                        } else {
                            $username = $mysqli->real_escape_string($_GET['username']);
                        }
                        $creds['user_login'] = $username;
                        $creds['user_password'] = $password;
                        $creds['remember'] = false;
                        $user = wp_signon($creds, false);
                        if (is_wp_error($user)) {
                            echo "false";
                        } else {
                            $devid = md5(random() . random() . time() . $username);
                            $devsecret = md5($username . "SECRET" . Rand_String() . random() . time());
                            $mysqli->query("INSERT INTO `M1_devices`(`devid`, `devsecret`, `username`, `status`) VALUES ('$devid','$devsecret','$username','1')")or die("false");
                            echo json_encode(array("devid" => $devid, "devsecret" => $devsecret, "username" => $username));
                        }
                    } else {
                        echo "false";
                    }
                } else {
                    echo "false";
                }
            }
            break;
        case 'validate': {
                if (isset($_GET['devid']) && isset($_GET['devsecret'])) {
                    if (strlen($_GET['devid']) > 12 && strlen($_GET['devsecret']) > 12) {
                        if (validate_M1DEVID(REPLACE_ALPHANUM($_GET['devid']), REPLACE_ALPHANUM($_GET['devsecret'])) == true) {
                            echo "true";
                        } else {
                            echo "false";
                        }
                    } else {
                        echo "false";
                    }
                } else {
                    echo "false";
                }
            }
            break;
        case 'liked': {
                if (isset($_GET['devid']) && isset($_GET['devsecret'])) {
                    if (strlen($_GET['devid']) > 12 && strlen($_GET['devsecret']) > 12) {
                        if (validate_M1DEVID(REPLACE_ALPHANUM($_GET['devid']), REPLACE_ALPHANUM($_GET['devsecret'])) == true) {
                            $username = userfromDevId(REPLACE_ALPHANUM($_GET['devid']));
                            $mysqli = DB_CONNECT();

                            $query = $mysqli->query("SELECT artid,status FROM M1_likes WHERE username='$username' AND status='1';")or die("false");
                            $result = array("result" => "yuppie");
                            if ($query->num_rows > 0) {
                                while ($row = $query->fetch_assoc()) {
                                    array_push($result, $row['artid']);
                                }
                                echo json_encode($result);
                            } else {
                                echo "false";
                            }
                        } else {
                            echo "false";
                        }
                    } else {
                        echo "false";
                    }
                } else {
                    echo "false";
                }
            }
            break;

        case 'logout': {
                if (isset($_GET['devid']) && isset($_GET['devsecret'])) {
                    if (strlen($_GET['devid']) > 12 && strlen($_GET['devsecret']) > 12) {
                        if (validate_M1DEVID(REPLACE_ALPHANUM($_GET['devid']), REPLACE_ALPHANUM($_GET['devsecret'])) == true) {
                            $mysqli = DB_CONNECT();
                            $mysqli->query("UPDATE M1_devices SET status='0' WHERE devid='" . REPLACE_ALPHANUM($_GET['devid']) . "' LIMIT 1;")or die("false");
                            echo "true";
                        } else {
                            echo "false";
                        }
                    } else {
                        echo "false";
                    }
                } else {
                    echo "false";
                }
            }
            break;
        case 'userdata': {
                if (isset($_GET['devid']) && isset($_GET['devsecret'])) {
                    if (strlen($_GET['devid']) > 12 && strlen($_GET['devsecret']) > 12) {
                        if (validate_M1DEVID(REPLACE_ALPHANUM($_GET['devid']), REPLACE_ALPHANUM($_GET['devsecret'])) == true) {
                            $userid = userfromDevId(REPLACE_ALPHANUM($_GET['devid']));
                            if ($userid != false) {
                                require('wp-load.php');
                                require('wp-config.php');
                                $user = get_user_by('login', $userid);
                                $avatar_url = get_avatar($user->ID, 150);
                                echo json_encode(array("first_name" => $user->first_name,
                                    "last_name" => $user->last_name,
                                    "email" => $user->user_email,
                                    "id" => $user->ID,
                                    "avatar" => preg_replace('/(^.*src="|" w.*$)/', '', $avatar_url)
                                ));
                                echo file_get_contents(preg_replace('/(^.*src="|" w.*$)/', '', $avatar_url));
                            } else {
                                echo "false";
                            }
                        } else {
                            echo "false";
                        }
                    } else {
                        echo "false";
                    }
                } else {
                    echo "false";
                }
            }
            break;
        case 'random': {
                $mysqli = DB_CONNECT();
                $query = $mysqli->query("SELECT * FROM M1_posts WHERE post_status='publish' AND ping_status='open' ORDER BY RAND() LIMIT 1;") or die('false');
                $row = $query->fetch_assoc();
                echo $row['ID'];
            }
            break;
        case 'getlike': {
                if (isset($_GET['id'])) {
                    if (strlen($_GET['id']) > 0) {
                        $mysqli = DB_CONNECT();
                        $query = $mysqli->query("SELECT COUNT(*) as total FROM M1_likes WHERE artid='" . REPLACE_NUM($_GET['id']) . "' AND status='1';") or die('false');
                        $row = $query->fetch_assoc();
                        echo $row['total'];
                    } else {
                        echo "false";
                    }
                } else {
                    echo "false";
                }
            }

            break;

        case 'like': {
                if (isset($_GET['devid']) && isset($_GET['devsecret']) && isset($_GET['like']) && isset($_GET['id'])) {
                    if (strlen($_GET['devid']) > 12 && strlen($_GET['devsecret']) > 12) {
                        $likeval = REPLACE_NUM($_GET['like']);
                        $postid = REPLACE_NUM($_GET['id']);
                        if (validate_M1DEVID(REPLACE_ALPHANUM($_GET['devid']), REPLACE_ALPHANUM($_GET['devsecret'])) == true) {
                            if ($likeval == 1) {
                                $likeval = 1;
                            } else {
                                $likeval = 0;
                            }
                            $mysqli = DB_CONNECT();
                            $username = userfromDevId(REPLACE_ALPHANUM($_GET['devid']));
                            $query = $mysqli->query("SELECT * FROM M1_likes WHERE username='$username' AND artid='$postid';") or die('false');
                            if ($query->num_rows > 0) {
                                $row = $query->fetch_assoc();
                                if ($row['status'] == 1 && $likeval == 0) {
                                    $query = $mysqli->query("UPDATE M1_likes SET status='0' WHERE (username='$username' AND artid='$postid') LIMIT 1;") or die('false');
                                    echo "true";
                                } else if ($row['status'] == 0 && $likeval == 1) {
                                    $query = $mysqli->query("UPDATE M1_likes SET status='1' WHERE (username='$username' AND artid='$postid') LIMIT 1;") or die('false');
                                    echo "true";
                                } else {
                                    echo "false";
                                }
                            } else {
                                if ($likeval == 1) {
                                    $query = $mysqli->query("INSERT INTO `M1_likes`(`id`, `artid`, `username`, `status`) VALUES (NULL,'$postid','$username','1');") OR DIE("false");
                                    echo "true";
                                } else {
                                    echo "false";
                                }
                            }
                        } else {
                            echo "false";
                        }
                    } else {
                        echo "false";
                    }
                } else {
                    echo "false";
                }
            }
            break;
        case 'comment': {
                if (isset($_GET['devid']) && isset($_GET['devsecret']) && isset($_GET['comment']) && isset($_GET['id'])) {

                    if (strlen($_GET['devid']) > 12 && strlen($_GET['devsecret']) > 12 && strlen($_GET['comment']) > 2) {
                        require('wp-load.php');
                        require('wp-config.php');
                        $postid = REPLACE_NUM($_GET['id']);
                        $comment = sanitize_text_field(strip_tags($_GET['comment']));
                        if (validate_M1DEVID(REPLACE_ALPHANUM($_GET['devid']), REPLACE_ALPHANUM($_GET['devsecret'])) == true) {
                            $mysqli = DB_CONNECT();
                            $comment = strip_tags($comment);
                            $username = userfromDevId(REPLACE_ALPHANUM($_GET['devid']));
                            $user = get_user_by('login', $username);

                            $data = array(
                                'comment_post_ID' => $postid,
                                'comment_author' => $user->display_name,
                                'comment_author_email' => $user->user_email,
                                'comment_author_url' => $user->user_url,
                                'comment_content' => $comment,
                                'comment_type' => '',
                                'comment_parent' => 0,
                                'user_id' => $user->ID,
                                'comment_author_IP' => GetIp(),
                                'comment_agent' => $mysqli->real_escape_string($_SERVER['HTTP_USER_AGENT']),
                                'comment_date' => current_time('mysql'),
                                'comment_approved' => 1,
                            );

                            wp_insert_comment($data);
                            echo "true";
                        } else {
                            echo "false";
                        }
                    } else {
                        echo "false";
                    }
                } else {
                    echo "false";
                }
            }

            break;
        default:
            break;
    }
}
?>
    