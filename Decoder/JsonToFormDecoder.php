<?php


namespace Mbright\Bundle\RepresentBundle\Decoder;


class JsonToFormDecoder implements DecoderInterface
{
    /**
     * Makes data decoded from JSON application/x-www-form-encoded compliant
     *
     * @param array $data
     */
    private function xWwwFormEncodedLike(&$data)
    {
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                // Encode recursively
                $this->xWwwFormEncodedLike($value);
            } elseif (false === $value) {
                // Checkbox-like behavior removes false data but PATCH HTTP method with just checkboxes does not work
                // To fix this issue we prefer transform false data to null
                // See https://github.com/FriendsOfSymfony/FOSRestBundle/pull/883
                $value = null;
            } elseif (!is_string($value)) {
                // Convert everything to string
                // true values will be converted to '1', this is the default checkbox behavior
                $value = strval($value);
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    public function decode($data)
    {
        $decodedData = @json_decode($data, true);
        if ($decodedData) {
            $this->xWwwFormEncodedLike($decodedData);
        }
        return $decodedData;
    }

}