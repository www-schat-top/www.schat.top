package my
 
func Max(x int, y int) int {
    var rtv int
    rtv = x
    if(x<y) {
      rtv = y
    }
    return rtv
}
