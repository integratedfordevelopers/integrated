<?php

namespace Integrated\Common\ContentType;

interface ContentTypeRepositoryInterface
{
    /**
     * @param string $id
     * @return ContentTypeInterface
     */
    public function find($id);
}