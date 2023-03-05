<?php

class event extends DB
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->_connect();
        $this->_table('events');
    }

    public function getDetailEvent($id)
    {
        return $this->db->rawQuery("SELECT ev.*,h.nbrPlace,m.image,m.libel,m.Time, m.description ,m.actors,  m.genre, m.date, m.Country, m.language, m.imdbRating, h.nbrPlace, h.numbre, h.numbre AS hallNumber ,ev.date as eventDate
                                    FROM 
                                        {$this->table} ev
                                    INNER JOIN 
                                        movie m
                                    ON 
                                        ev.movie = m.id
                                    INNER JOIN 
                                        halls h
                                    ON 
                                        ev.hall = h.id
                                    WHERE 
                                        ev.id = {$id}
                                    ")[0];
    }

    /**
     * @return bool true when event exists
     * @throws Exception
     */
    public function existsEvent($id, $date, $hall): bool
    {
        return (bool)$this->db->rawQuery("SELECT EXISTS( SELECT *
                                                            FROM {$this->table}
                                                        WHERE
                                                            `date` = '{$date}'
                                                        AND 
                                                            hall = {$hall}
                                                        AND
                                                            id != '{$id}'
                                                        ) AS rep;
                                        ")[0]['rep'];
    }
    public function getAllEvents()
    {
        return $this->db->rawQuery("SELECT {$this->table}.* , m.image, m.libel, m.Time, m.description ,m.actors,  m.genre, m.date, m.Country, m.language, m.imdbRating, h.nbrPlace, h.numbre, h.numbre AS hallNumber ,{$this->table}.date as eventDate
                                    FROM
                                        {$this->table}
                                    INNER JOIN 
                                        movie m
                                    ON 
                                        {$this->table}.movie = m.id
                                    INNER JOIN
                                        halls h
                                    ON
                                        {$this->table}.hall = h.id
                                    where
                                        {$this->table}.date >= CURDATE()
                                    ");
    }
    public function getAllEventsByDate($date)
    {
        return $this->db->rawQuery("SELECT {$this->table}.* , m.image, m.libel, m.Time, m.description ,m.actors,  m.genre, m.date, m.Country, m.language, m.imdbRating, h.nbrPlace, h.numbre, h.numbre AS hallNumber ,{$this->table}.date as eventDate
                                    FROM
                                        {$this->table}
                                    INNER JOIN 
                                        movie m
                                    ON 
                                        {$this->table}.movie = m.id
                                    INNER JOIN
                                        halls h
                                    ON
                                        {$this->table}.hall = h.id
                                    where
                                        {$this->table}.date = '{$date}'
                                    ");
    }
}
