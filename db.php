--
-- Table structure for table `bris_product_custom_field`
--

CREATE TABLE IF NOT EXISTS `bris_product_custom_field` (
`product_id` int(11) NOT NULL,
  `msrp` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `item_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bris_product_custom_field`
--
ALTER TABLE `bris_product_custom_field`
 ADD PRIMARY KEY (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bris_product_custom_field`
--
ALTER TABLE `bris_product_custom_field`
MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;