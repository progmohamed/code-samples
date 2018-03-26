<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OldVideo
 *
 * @ORM\Table(name="old_video", indexes={@ORM\Index(name="userid", columns={"userid"}), @ORM\Index(name="featured", columns={"featured"}), @ORM\Index(name="last_viewed", columns={"last_viewed"}), @ORM\Index(name="rating", columns={"rating"}), @ORM\Index(name="comments_count", columns={"comments_count"}), @ORM\Index(name="last_viewed_2", columns={"last_viewed"}), @ORM\Index(name="status", columns={"status", "active", "broadcast", "userid"}), @ORM\Index(name="userid_2", columns={"userid"})})
 * @ORM\Entity
 */
class OldVideo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="videoid", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $videoid;

    /**
     * @var string
     *
     * @ORM\Column(name="videokey", type="text", length=16777215, nullable=false)
     */
    private $videokey;

    /**
     * @var string
     *
     * @ORM\Column(name="video_password", type="string", length=255, nullable=false)
     */
    private $videoPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="video_users", type="text", length=65535, nullable=false)
     */
    private $videoUsers;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="text", length=65535, nullable=false)
     */
    private $username;

    /**
     * @var integer
     *
     * @ORM\Column(name="userid", type="integer", nullable=false)
     */
    private $userid;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", length=65535, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="flv", type="text", length=16777215, nullable=false)
     */
    private $flv;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=32, nullable=false)
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="file_directory", type="string", length=25, nullable=false)
     */
    private $fileDirectory;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="tags", type="text", length=16777215, nullable=false)
     */
    private $tags;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=200, nullable=false)
     */
    private $category = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="category_parents", type="text", length=65535, nullable=false)
     */
    private $categoryParents;

    /**
     * @var string
     *
     * @ORM\Column(name="broadcast", type="string", length=10, nullable=false)
     */
    private $broadcast = '';

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="text", length=16777215, nullable=true)
     */
    private $location;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datecreated", type="date", nullable=true)
     */
    private $datecreated;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="text", length=16777215, nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="allow_embedding", type="string", length=3, nullable=false)
     */
    private $allowEmbedding = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="rating", type="integer", nullable=false)
     */
    private $rating = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="rated_by", type="string", length=20, nullable=false)
     */
    private $ratedBy = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="voter_ids", type="text", length=16777215, nullable=false)
     */
    private $voterIds;

    /**
     * @var string
     *
     * @ORM\Column(name="allow_comments", type="string", length=3, nullable=false)
     */
    private $allowComments = '';

    /**
     * @var string
     *
     * @ORM\Column(name="comment_voting", type="string", length=3, nullable=false)
     */
    private $commentVoting = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="comments_count", type="integer", nullable=false)
     */
    private $commentsCount = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_commented", type="datetime", nullable=false)
     */
    private $lastCommented;

    /**
     * @var string
     *
     * @ORM\Column(name="featured", type="string", length=3, nullable=false)
     */
    private $featured = 'no';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="featured_date", type="datetime", nullable=false)
     */
    private $featuredDate;

    /**
     * @var string
     *
     * @ORM\Column(name="featured_description", type="text", length=16777215, nullable=false)
     */
    private $featuredDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="allow_rating", type="string", length=3, nullable=false)
     */
    private $allowRating = '';

    /**
     * @var string
     *
     * @ORM\Column(name="active", type="string", length=3, nullable=false)
     */
    private $active = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="favourite_count", type="string", length=15, nullable=false)
     */
    private $favouriteCount = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="playlist_count", type="string", length=15, nullable=false)
     */
    private $playlistCount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="views", type="bigint", nullable=false)
     */
    private $views = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_viewed", type="datetime", nullable=false)
     */
    private $lastViewed = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false)
     */
    private $dateAdded = '0000-00-00 00:00:00';

    /**
     * @var string
     *
     * @ORM\Column(name="flagged", type="string", length=3, nullable=false)
     */
    private $flagged = 'no';

    /**
     * @var string
     *
     * @ORM\Column(name="duration", type="string", length=20, nullable=false)
     */
    private $duration = '00';

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = 'Processing';

    /**
     * @var string
     *
     * @ORM\Column(name="failed_reason", type="string", nullable=false)
     */
    private $failedReason = 'none';

    /**
     * @var string
     *
     * @ORM\Column(name="flv_file_url", type="text", length=65535, nullable=true)
     */
    private $flvFileUrl;

    /**
     * @var integer
     *
     * @ORM\Column(name="default_thumb", type="integer", nullable=false)
     */
    private $defaultThumb = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="aspect_ratio", type="string", length=10, nullable=false)
     */
    private $aspectRatio;

    /**
     * @var string
     *
     * @ORM\Column(name="embed_code", type="text", length=65535, nullable=false)
     */
    private $embedCode;

    /**
     * @var string
     *
     * @ORM\Column(name="refer_url", type="text", length=65535, nullable=false)
     */
    private $referUrl;

    /**
     * @var integer
     *
     * @ORM\Column(name="downloads", type="bigint", nullable=false)
     */
    private $downloads;

    /**
     * @var string
     *
     * @ORM\Column(name="uploader_ip", type="string", length=20, nullable=false)
     */
    private $uploaderIp;

    /**
     * @var string
     *
     * @ORM\Column(name="mass_embed_status", type="string", nullable=false)
     */
    private $massEmbedStatus = 'no';

    /**
     * @var string
     *
     * @ORM\Column(name="is_hd", type="string", nullable=false)
     */
    private $isHd = 'no';

    /**
     * @var string
     *
     * @ORM\Column(name="unique_embed_code", type="string", length=50, nullable=false)
     */
    private $uniqueEmbedCode;

    /**
     * @var string
     *
     * @ORM\Column(name="remote_play_url", type="text", length=65535, nullable=false)
     */
    private $remotePlayUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="video_files", type="text", length=255, nullable=false)
     */
    private $videoFiles;

    /**
     * @var string
     *
     * @ORM\Column(name="server_ip", type="string", length=20, nullable=false)
     */
    private $serverIp;

    /**
     * @var string
     *
     * @ORM\Column(name="file_server_path", type="text", length=65535, nullable=false)
     */
    private $fileServerPath;

    /**
     * @var string
     *
     * @ORM\Column(name="files_thumbs_path", type="text", length=65535, nullable=false)
     */
    private $filesThumbsPath;

    /**
     * @var string
     *
     * @ORM\Column(name="file_thumbs_count", type="string", length=30, nullable=false)
     */
    private $fileThumbsCount;

    /**
     * @var string
     *
     * @ORM\Column(name="has_hq", type="string", nullable=false)
     */
    private $hasHq = 'no';

    /**
     * @var string
     *
     * @ORM\Column(name="has_mobile", type="string", nullable=false)
     */
    private $hasMobile = 'no';

    /**
     * @var string
     *
     * @ORM\Column(name="filegrp_size", type="string", length=30, nullable=false)
     */
    private $filegrpSize;

    /**
     * @var integer
     *
     * @ORM\Column(name="process_status", type="bigint", nullable=false)
     */
    private $processStatus = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="has_hd", type="string", nullable=false)
     */
    private $hasHd = 'no';

    /**
     * @var string
     *
     * @ORM\Column(name="video_version", type="string", length=30, nullable=false)
     */
    private $videoVersion = '2.6';

    /**
     * @var string
     *
     * @ORM\Column(name="extras", type="string", length=225, nullable=false)
     */
    private $extras;

    /**
     * @var string
     *
     * @ORM\Column(name="thumbs_version", type="string", length=5, nullable=false)
     */
    private $thumbsVersion = '2.6';

    /**
     * @var integer
     *
     * @ORM\Column(name="reciter", type="integer", nullable=false)
     */
    private $reciter;

    /**
     * @var integer
     *
     * @ORM\Column(name="sura", type="integer", nullable=false)
     */
    private $sura;

    /**
     * @var integer
     *
     * @ORM\Column(name="joz", type="integer", nullable=false)
     */
    private $joz;

    /**
     * @var integer
     *
     * @ORM\Column(name="hizb", type="integer", nullable=false)
     */
    private $hizb;

    /**
     * @var integer
     *
     * @ORM\Column(name="narrative", type="integer", nullable=false)
     */
    private $narrative;

    /**
     * @var string
     *
     * @ORM\Column(name="in_editor_pick", type="string", length=255, nullable=true)
     */
    private $inEditorPick = 'no';

    /**
     * @var string
     *
     * @ORM\Column(name="cfld_sura", type="string", length=255, nullable=false)
     */
    private $cfldSura;



    /**
     * Get videoid
     *
     * @return integer
     */
    public function getVideoid()
    {
        return $this->videoid;
    }

    /**
     * Set videokey
     *
     * @param string $videokey
     *
     * @return OldVideo
     */
    public function setVideokey($videokey)
    {
        $this->videokey = $videokey;

        return $this;
    }

    /**
     * Get videokey
     *
     * @return string
     */
    public function getVideokey()
    {
        return $this->videokey;
    }

    /**
     * Set videoPassword
     *
     * @param string $videoPassword
     *
     * @return OldVideo
     */
    public function setVideoPassword($videoPassword)
    {
        $this->videoPassword = $videoPassword;

        return $this;
    }

    /**
     * Get videoPassword
     *
     * @return string
     */
    public function getVideoPassword()
    {
        return $this->videoPassword;
    }

    /**
     * Set videoUsers
     *
     * @param string $videoUsers
     *
     * @return OldVideo
     */
    public function setVideoUsers($videoUsers)
    {
        $this->videoUsers = $videoUsers;

        return $this;
    }

    /**
     * Get videoUsers
     *
     * @return string
     */
    public function getVideoUsers()
    {
        return $this->videoUsers;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return OldVideo
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set userid
     *
     * @param integer $userid
     *
     * @return OldVideo
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return integer
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return OldVideo
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set flv
     *
     * @param string $flv
     *
     * @return OldVideo
     */
    public function setFlv($flv)
    {
        $this->flv = $flv;

        return $this;
    }

    /**
     * Get flv
     *
     * @return string
     */
    public function getFlv()
    {
        return $this->flv;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     *
     * @return OldVideo
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set fileDirectory
     *
     * @param string $fileDirectory
     *
     * @return OldVideo
     */
    public function setFileDirectory($fileDirectory)
    {
        $this->fileDirectory = $fileDirectory;

        return $this;
    }

    /**
     * Get fileDirectory
     *
     * @return string
     */
    public function getFileDirectory()
    {
        return $this->fileDirectory;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return OldVideo
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set tags
     *
     * @param string $tags
     *
     * @return OldVideo
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return OldVideo
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set categoryParents
     *
     * @param string $categoryParents
     *
     * @return OldVideo
     */
    public function setCategoryParents($categoryParents)
    {
        $this->categoryParents = $categoryParents;

        return $this;
    }

    /**
     * Get categoryParents
     *
     * @return string
     */
    public function getCategoryParents()
    {
        return $this->categoryParents;
    }

    /**
     * Set broadcast
     *
     * @param string $broadcast
     *
     * @return OldVideo
     */
    public function setBroadcast($broadcast)
    {
        $this->broadcast = $broadcast;

        return $this;
    }

    /**
     * Get broadcast
     *
     * @return string
     */
    public function getBroadcast()
    {
        return $this->broadcast;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return OldVideo
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set datecreated
     *
     * @param \DateTime $datecreated
     *
     * @return OldVideo
     */
    public function setDatecreated($datecreated)
    {
        $this->datecreated = $datecreated;

        return $this;
    }

    /**
     * Get datecreated
     *
     * @return \DateTime
     */
    public function getDatecreated()
    {
        return $this->datecreated;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return OldVideo
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set allowEmbedding
     *
     * @param string $allowEmbedding
     *
     * @return OldVideo
     */
    public function setAllowEmbedding($allowEmbedding)
    {
        $this->allowEmbedding = $allowEmbedding;

        return $this;
    }

    /**
     * Get allowEmbedding
     *
     * @return string
     */
    public function getAllowEmbedding()
    {
        return $this->allowEmbedding;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     *
     * @return OldVideo
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set ratedBy
     *
     * @param string $ratedBy
     *
     * @return OldVideo
     */
    public function setRatedBy($ratedBy)
    {
        $this->ratedBy = $ratedBy;

        return $this;
    }

    /**
     * Get ratedBy
     *
     * @return string
     */
    public function getRatedBy()
    {
        return $this->ratedBy;
    }

    /**
     * Set voterIds
     *
     * @param string $voterIds
     *
     * @return OldVideo
     */
    public function setVoterIds($voterIds)
    {
        $this->voterIds = $voterIds;

        return $this;
    }

    /**
     * Get voterIds
     *
     * @return string
     */
    public function getVoterIds()
    {
        return $this->voterIds;
    }

    /**
     * Set allowComments
     *
     * @param string $allowComments
     *
     * @return OldVideo
     */
    public function setAllowComments($allowComments)
    {
        $this->allowComments = $allowComments;

        return $this;
    }

    /**
     * Get allowComments
     *
     * @return string
     */
    public function getAllowComments()
    {
        return $this->allowComments;
    }

    /**
     * Set commentVoting
     *
     * @param string $commentVoting
     *
     * @return OldVideo
     */
    public function setCommentVoting($commentVoting)
    {
        $this->commentVoting = $commentVoting;

        return $this;
    }

    /**
     * Get commentVoting
     *
     * @return string
     */
    public function getCommentVoting()
    {
        return $this->commentVoting;
    }

    /**
     * Set commentsCount
     *
     * @param integer $commentsCount
     *
     * @return OldVideo
     */
    public function setCommentsCount($commentsCount)
    {
        $this->commentsCount = $commentsCount;

        return $this;
    }

    /**
     * Get commentsCount
     *
     * @return integer
     */
    public function getCommentsCount()
    {
        return $this->commentsCount;
    }

    /**
     * Set lastCommented
     *
     * @param \DateTime $lastCommented
     *
     * @return OldVideo
     */
    public function setLastCommented($lastCommented)
    {
        $this->lastCommented = $lastCommented;

        return $this;
    }

    /**
     * Get lastCommented
     *
     * @return \DateTime
     */
    public function getLastCommented()
    {
        return $this->lastCommented;
    }

    /**
     * Set featured
     *
     * @param string $featured
     *
     * @return OldVideo
     */
    public function setFeatured($featured)
    {
        $this->featured = $featured;

        return $this;
    }

    /**
     * Get featured
     *
     * @return string
     */
    public function getFeatured()
    {
        return $this->featured;
    }

    /**
     * Set featuredDate
     *
     * @param \DateTime $featuredDate
     *
     * @return OldVideo
     */
    public function setFeaturedDate($featuredDate)
    {
        $this->featuredDate = $featuredDate;

        return $this;
    }

    /**
     * Get featuredDate
     *
     * @return \DateTime
     */
    public function getFeaturedDate()
    {
        return $this->featuredDate;
    }

    /**
     * Set featuredDescription
     *
     * @param string $featuredDescription
     *
     * @return OldVideo
     */
    public function setFeaturedDescription($featuredDescription)
    {
        $this->featuredDescription = $featuredDescription;

        return $this;
    }

    /**
     * Get featuredDescription
     *
     * @return string
     */
    public function getFeaturedDescription()
    {
        return $this->featuredDescription;
    }

    /**
     * Set allowRating
     *
     * @param string $allowRating
     *
     * @return OldVideo
     */
    public function setAllowRating($allowRating)
    {
        $this->allowRating = $allowRating;

        return $this;
    }

    /**
     * Get allowRating
     *
     * @return string
     */
    public function getAllowRating()
    {
        return $this->allowRating;
    }

    /**
     * Set active
     *
     * @param string $active
     *
     * @return OldVideo
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return string
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set favouriteCount
     *
     * @param string $favouriteCount
     *
     * @return OldVideo
     */
    public function setFavouriteCount($favouriteCount)
    {
        $this->favouriteCount = $favouriteCount;

        return $this;
    }

    /**
     * Get favouriteCount
     *
     * @return string
     */
    public function getFavouriteCount()
    {
        return $this->favouriteCount;
    }

    /**
     * Set playlistCount
     *
     * @param string $playlistCount
     *
     * @return OldVideo
     */
    public function setPlaylistCount($playlistCount)
    {
        $this->playlistCount = $playlistCount;

        return $this;
    }

    /**
     * Get playlistCount
     *
     * @return string
     */
    public function getPlaylistCount()
    {
        return $this->playlistCount;
    }

    /**
     * Set views
     *
     * @param integer $views
     *
     * @return OldVideo
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set lastViewed
     *
     * @param \DateTime $lastViewed
     *
     * @return OldVideo
     */
    public function setLastViewed($lastViewed)
    {
        $this->lastViewed = $lastViewed;

        return $this;
    }

    /**
     * Get lastViewed
     *
     * @return \DateTime
     */
    public function getLastViewed()
    {
        return $this->lastViewed;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     *
     * @return OldVideo
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * Set flagged
     *
     * @param string $flagged
     *
     * @return OldVideo
     */
    public function setFlagged($flagged)
    {
        $this->flagged = $flagged;

        return $this;
    }

    /**
     * Get flagged
     *
     * @return string
     */
    public function getFlagged()
    {
        return $this->flagged;
    }

    /**
     * Set duration
     *
     * @param string $duration
     *
     * @return OldVideo
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return string
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return OldVideo
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set failedReason
     *
     * @param string $failedReason
     *
     * @return OldVideo
     */
    public function setFailedReason($failedReason)
    {
        $this->failedReason = $failedReason;

        return $this;
    }

    /**
     * Get failedReason
     *
     * @return string
     */
    public function getFailedReason()
    {
        return $this->failedReason;
    }

    /**
     * Set flvFileUrl
     *
     * @param string $flvFileUrl
     *
     * @return OldVideo
     */
    public function setFlvFileUrl($flvFileUrl)
    {
        $this->flvFileUrl = $flvFileUrl;

        return $this;
    }

    /**
     * Get flvFileUrl
     *
     * @return string
     */
    public function getFlvFileUrl()
    {
        return $this->flvFileUrl;
    }

    /**
     * Set defaultThumb
     *
     * @param integer $defaultThumb
     *
     * @return OldVideo
     */
    public function setDefaultThumb($defaultThumb)
    {
        $this->defaultThumb = $defaultThumb;

        return $this;
    }

    /**
     * Get defaultThumb
     *
     * @return integer
     */
    public function getDefaultThumb()
    {
        return $this->defaultThumb;
    }

    /**
     * Set aspectRatio
     *
     * @param string $aspectRatio
     *
     * @return OldVideo
     */
    public function setAspectRatio($aspectRatio)
    {
        $this->aspectRatio = $aspectRatio;

        return $this;
    }

    /**
     * Get aspectRatio
     *
     * @return string
     */
    public function getAspectRatio()
    {
        return $this->aspectRatio;
    }

    /**
     * Set embedCode
     *
     * @param string $embedCode
     *
     * @return OldVideo
     */
    public function setEmbedCode($embedCode)
    {
        $this->embedCode = $embedCode;

        return $this;
    }

    /**
     * Get embedCode
     *
     * @return string
     */
    public function getEmbedCode()
    {
        return $this->embedCode;
    }

    /**
     * Set referUrl
     *
     * @param string $referUrl
     *
     * @return OldVideo
     */
    public function setReferUrl($referUrl)
    {
        $this->referUrl = $referUrl;

        return $this;
    }

    /**
     * Get referUrl
     *
     * @return string
     */
    public function getReferUrl()
    {
        return $this->referUrl;
    }

    /**
     * Set downloads
     *
     * @param integer $downloads
     *
     * @return OldVideo
     */
    public function setDownloads($downloads)
    {
        $this->downloads = $downloads;

        return $this;
    }

    /**
     * Get downloads
     *
     * @return integer
     */
    public function getDownloads()
    {
        return $this->downloads;
    }

    /**
     * Set uploaderIp
     *
     * @param string $uploaderIp
     *
     * @return OldVideo
     */
    public function setUploaderIp($uploaderIp)
    {
        $this->uploaderIp = $uploaderIp;

        return $this;
    }

    /**
     * Get uploaderIp
     *
     * @return string
     */
    public function getUploaderIp()
    {
        return $this->uploaderIp;
    }

    /**
     * Set massEmbedStatus
     *
     * @param string $massEmbedStatus
     *
     * @return OldVideo
     */
    public function setMassEmbedStatus($massEmbedStatus)
    {
        $this->massEmbedStatus = $massEmbedStatus;

        return $this;
    }

    /**
     * Get massEmbedStatus
     *
     * @return string
     */
    public function getMassEmbedStatus()
    {
        return $this->massEmbedStatus;
    }

    /**
     * Set isHd
     *
     * @param string $isHd
     *
     * @return OldVideo
     */
    public function setIsHd($isHd)
    {
        $this->isHd = $isHd;

        return $this;
    }

    /**
     * Get isHd
     *
     * @return string
     */
    public function getIsHd()
    {
        return $this->isHd;
    }

    /**
     * Set uniqueEmbedCode
     *
     * @param string $uniqueEmbedCode
     *
     * @return OldVideo
     */
    public function setUniqueEmbedCode($uniqueEmbedCode)
    {
        $this->uniqueEmbedCode = $uniqueEmbedCode;

        return $this;
    }

    /**
     * Get uniqueEmbedCode
     *
     * @return string
     */
    public function getUniqueEmbedCode()
    {
        return $this->uniqueEmbedCode;
    }

    /**
     * Set remotePlayUrl
     *
     * @param string $remotePlayUrl
     *
     * @return OldVideo
     */
    public function setRemotePlayUrl($remotePlayUrl)
    {
        $this->remotePlayUrl = $remotePlayUrl;

        return $this;
    }

    /**
     * Get remotePlayUrl
     *
     * @return string
     */
    public function getRemotePlayUrl()
    {
        return $this->remotePlayUrl;
    }

    /**
     * Set videoFiles
     *
     * @param string $videoFiles
     *
     * @return OldVideo
     */
    public function setVideoFiles($videoFiles)
    {
        $this->videoFiles = $videoFiles;

        return $this;
    }

    /**
     * Get videoFiles
     *
     * @return string
     */
    public function getVideoFiles()
    {
        return $this->videoFiles;
    }

    /**
     * Set serverIp
     *
     * @param string $serverIp
     *
     * @return OldVideo
     */
    public function setServerIp($serverIp)
    {
        $this->serverIp = $serverIp;

        return $this;
    }

    /**
     * Get serverIp
     *
     * @return string
     */
    public function getServerIp()
    {
        return $this->serverIp;
    }

    /**
     * Set fileServerPath
     *
     * @param string $fileServerPath
     *
     * @return OldVideo
     */
    public function setFileServerPath($fileServerPath)
    {
        $this->fileServerPath = $fileServerPath;

        return $this;
    }

    /**
     * Get fileServerPath
     *
     * @return string
     */
    public function getFileServerPath()
    {
        return $this->fileServerPath;
    }

    /**
     * Set filesThumbsPath
     *
     * @param string $filesThumbsPath
     *
     * @return OldVideo
     */
    public function setFilesThumbsPath($filesThumbsPath)
    {
        $this->filesThumbsPath = $filesThumbsPath;

        return $this;
    }

    /**
     * Get filesThumbsPath
     *
     * @return string
     */
    public function getFilesThumbsPath()
    {
        return $this->filesThumbsPath;
    }

    /**
     * Set fileThumbsCount
     *
     * @param string $fileThumbsCount
     *
     * @return OldVideo
     */
    public function setFileThumbsCount($fileThumbsCount)
    {
        $this->fileThumbsCount = $fileThumbsCount;

        return $this;
    }

    /**
     * Get fileThumbsCount
     *
     * @return string
     */
    public function getFileThumbsCount()
    {
        return $this->fileThumbsCount;
    }

    /**
     * Set hasHq
     *
     * @param string $hasHq
     *
     * @return OldVideo
     */
    public function setHasHq($hasHq)
    {
        $this->hasHq = $hasHq;

        return $this;
    }

    /**
     * Get hasHq
     *
     * @return string
     */
    public function getHasHq()
    {
        return $this->hasHq;
    }

    /**
     * Set hasMobile
     *
     * @param string $hasMobile
     *
     * @return OldVideo
     */
    public function setHasMobile($hasMobile)
    {
        $this->hasMobile = $hasMobile;

        return $this;
    }

    /**
     * Get hasMobile
     *
     * @return string
     */
    public function getHasMobile()
    {
        return $this->hasMobile;
    }

    /**
     * Set filegrpSize
     *
     * @param string $filegrpSize
     *
     * @return OldVideo
     */
    public function setFilegrpSize($filegrpSize)
    {
        $this->filegrpSize = $filegrpSize;

        return $this;
    }

    /**
     * Get filegrpSize
     *
     * @return string
     */
    public function getFilegrpSize()
    {
        return $this->filegrpSize;
    }

    /**
     * Set processStatus
     *
     * @param integer $processStatus
     *
     * @return OldVideo
     */
    public function setProcessStatus($processStatus)
    {
        $this->processStatus = $processStatus;

        return $this;
    }

    /**
     * Get processStatus
     *
     * @return integer
     */
    public function getProcessStatus()
    {
        return $this->processStatus;
    }

    /**
     * Set hasHd
     *
     * @param string $hasHd
     *
     * @return OldVideo
     */
    public function setHasHd($hasHd)
    {
        $this->hasHd = $hasHd;

        return $this;
    }

    /**
     * Get hasHd
     *
     * @return string
     */
    public function getHasHd()
    {
        return $this->hasHd;
    }

    /**
     * Set videoVersion
     *
     * @param string $videoVersion
     *
     * @return OldVideo
     */
    public function setVideoVersion($videoVersion)
    {
        $this->videoVersion = $videoVersion;

        return $this;
    }

    /**
     * Get videoVersion
     *
     * @return string
     */
    public function getVideoVersion()
    {
        return $this->videoVersion;
    }

    /**
     * Set extras
     *
     * @param string $extras
     *
     * @return OldVideo
     */
    public function setExtras($extras)
    {
        $this->extras = $extras;

        return $this;
    }

    /**
     * Get extras
     *
     * @return string
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Set thumbsVersion
     *
     * @param string $thumbsVersion
     *
     * @return OldVideo
     */
    public function setThumbsVersion($thumbsVersion)
    {
        $this->thumbsVersion = $thumbsVersion;

        return $this;
    }

    /**
     * Get thumbsVersion
     *
     * @return string
     */
    public function getThumbsVersion()
    {
        return $this->thumbsVersion;
    }

    /**
     * Set reciter
     *
     * @param integer $reciter
     *
     * @return OldVideo
     */
    public function setReciter($reciter)
    {
        $this->reciter = $reciter;

        return $this;
    }

    /**
     * Get reciter
     *
     * @return integer
     */
    public function getReciter()
    {
        return $this->reciter;
    }

    /**
     * Set sura
     *
     * @param integer $sura
     *
     * @return OldVideo
     */
    public function setSura($sura)
    {
        $this->sura = $sura;

        return $this;
    }

    /**
     * Get sura
     *
     * @return integer
     */
    public function getSura()
    {
        return $this->sura;
    }

    /**
     * Set joz
     *
     * @param integer $joz
     *
     * @return OldVideo
     */
    public function setJoz($joz)
    {
        $this->joz = $joz;

        return $this;
    }

    /**
     * Get joz
     *
     * @return integer
     */
    public function getJoz()
    {
        return $this->joz;
    }

    /**
     * Set hizb
     *
     * @param integer $hizb
     *
     * @return OldVideo
     */
    public function setHizb($hizb)
    {
        $this->hizb = $hizb;

        return $this;
    }

    /**
     * Get hizb
     *
     * @return integer
     */
    public function getHizb()
    {
        return $this->hizb;
    }

    /**
     * Set narrative
     *
     * @param integer $narrative
     *
     * @return OldVideo
     */
    public function setNarrative($narrative)
    {
        $this->narrative = $narrative;

        return $this;
    }

    /**
     * Get narrative
     *
     * @return integer
     */
    public function getNarrative()
    {
        return $this->narrative;
    }

    /**
     * Set inEditorPick
     *
     * @param string $inEditorPick
     *
     * @return OldVideo
     */
    public function setInEditorPick($inEditorPick)
    {
        $this->inEditorPick = $inEditorPick;

        return $this;
    }

    /**
     * Get inEditorPick
     *
     * @return string
     */
    public function getInEditorPick()
    {
        return $this->inEditorPick;
    }

    /**
     * Set cfldSura
     *
     * @param string $cfldSura
     *
     * @return OldVideo
     */
    public function setCfldSura($cfldSura)
    {
        $this->cfldSura = $cfldSura;

        return $this;
    }

    /**
     * Get cfldSura
     *
     * @return string
     */
    public function getCfldSura()
    {
        return $this->cfldSura;
    }
}
