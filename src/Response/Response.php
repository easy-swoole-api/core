<?php
/**
 * Note:     [Description]
 * Author:   longhui.huang <1592328848@qq.com>
 * DateTime: 2024/1/23 10:04
 */
declare(strict_types=1);

namespace EasySwooleApi\Core\Response;

class Response
{
    /** @var \EasySwoole\Http\Response */
    private $esResponse;

    // 原始数据
    protected $data;

    // 当前的contentType
    protected $contentType = 'text/html';

    // 字符集
    protected $charset = 'utf-8';

    //状态
    protected $code = 200;

    // 输出参数
    protected $options = [];
    // header参数
    protected $header = [];

    protected $content = null;

    public function __construct(\EasySwoole\Http\Response $esResponse, $data = '', $code = 200, array $header = [], $options = [])
    {
        $this->esResponse = $esResponse;

        $this->data($data);
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $this->contentType($this->contentType, $this->charset);
        $this->header = array_merge($this->header, $header);
        $this->code   = $code;
    }

    /**
     * 创建Response对象
     *
     * @access public
     *
     * @param mixed  $data    输出数据
     * @param string $type    输出类型
     * @param int    $code
     * @param array  $header
     * @param array  $options 输出参数
     *
     * @return Response|Json
     */
    public static function create(\EasySwoole\Http\Response $esResponse, $data = '', $type = '', $code = 200, array $header = [], $options = [])
    {
        $class = false !== strpos($type, '\\') ? $type : '\\EasySwooleApi\\Core\\Response\\' . ucfirst(strtolower($type));
        if (class_exists($class)) {
            $response = new $class($esResponse, $data, $code, $header, $options);
        } else {
            $response = new static($esResponse, $code, $header, $options);
        }

        return $response;
    }

    /**
     * 输出数据设置
     *
     * @access public
     *
     * @param mixed $data 输出数据
     *
     * @return $this
     */
    public function data($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 页面输出类型
     *
     * @param string $contentType 输出类型
     * @param string $charset     输出编码
     *
     * @return $this
     */
    public function contentType(string $contentType, string $charset = 'utf-8')
    {
        $this->header['Content-Type'] = $contentType . '; charset=' . $charset;
        return $this;
    }

    /**
     * 获取输出数据
     * @return mixed
     */
    public function getContent()
    {
        if (null == $this->content) {
            $content = $this->output($this->data);

            if (null !== $content && !is_string($content) && !is_numeric($content) && !is_callable([
                    $content,
                    '__toString',
                ])
            ) {
                throw new \InvalidArgumentException(sprintf('variable type error： %s', gettype($content)));
            }

            $this->content = (string) $content;
        }
        return $this->content;
    }

    /**
     * 发送数据到客户端
     *
     * @access public
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function send()
    {
        // 处理输出数据
        $data = $this->getContent();

        if (!$this->esResponse->isEndResponse()) {
            // 发送状态码
            $this->esResponse->withStatus($this->code);
            // 发送头部信息
            foreach ($this->header as $name => $val) {
                $this->esResponse->withHeader($name, $val);
            }
        }

        $this->esResponse->write($data);

        return false;
    }

    /**
     * 页面缓存控制
     * @param string $cache 状态码
     * @return $this
     */
    public function cacheControl($cache)
    {
        $this->header['Cache-control'] = $cache;
        return $this;
    }
}
