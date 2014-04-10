<?php

if ( $result ) {
	printf( '<pre>%s</pre>', print_r( $result, true ) );
} else {
	_e( 'Unable to retrieve the result', 'github-api' );
}
